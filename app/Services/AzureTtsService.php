<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Integrasi Azure AI Speech (Neural TTS) untuk generate audio listening
 * TOEFL dari transkrip "Woman: ... Man: ...". Pola class ini meniru
 * BaseOpenRouterService.php supaya konsisten dengan service eksternal lain.
 */
class AzureTtsService
{
    protected string $key;
    protected string $region;
    protected string $narratorIntro;

    public function __construct()
    {
        $this->key = (string) config('services.azure_speech.key', '');
        $this->region = (string) config('services.azure_speech.region', 'southeastasia');
        $this->narratorIntro = (string) config('services.azure_speech.narrator_intro', 'Listen to the following conversation.');
    }

    /**
     * Generate 1 audio MP3 dari transkrip, simpan ke storage, kembalikan
     * path relatif (siap dipakai untuk kolom audio_path).
     */
    public function generateFromTranscript(string $transcript, string $voiceWoman, string $voiceMan, string $storeFolder): string
    {
        $ssml = $this->buildDialogueSsml($transcript, $voiceWoman, $voiceMan);
        $binary = $this->synthesizeOne($ssml);

        $filename = $storeFolder . '/' . Str::uuid() . '.mp3';
        Storage::disk('public')->put($filename, $binary);

        return $filename;
    }

    /**
    * Generate BANYAK audio dalam satu request HTTP.
     *
     * @param array<int, array{id:int, transcript:string}> $items
     * @return array{success: array<int,string>, failed: array<int,string>} id => path (sukses) / id => pesan error (gagal)
     */
    public function generateBatch(array $items, string $voiceWoman, string $voiceMan, string $storeFolder, int $batchSize = 10): array
    {
        $success = [];
        $failed = [];

        foreach ($items as $item) {
            try {
                $path = $this->generateFromTranscript(
                    $item['transcript'],
                    $voiceWoman,
                    $voiceMan,
                    $storeFolder
                );
                $success[$item['id']] = $path;
            } catch (\Throwable $e) {
                $failed[$item['id']] = $e->getMessage();
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    /**
     * Generate audio preview singkat untuk 1 suara (dipakai di Voice
     * Picker). Di-cache di disk per voice_id supaya klik ulang tidak
     * memanggil Azure lagi (hemat kuota karakter).
     */
    public function previewVoice(string $voiceId): string
    {
        $cachePath = 'voice-previews/' . $voiceId . '.mp3';

        if (Storage::disk('public')->exists($cachePath)) {
            return $cachePath;
        }

        $sampleText = 'Did you submit the budget proposal yet?';
        $ssml = $this->wrapSsml('<voice name="' . $voiceId . '">' . $this->escapeXml($sampleText) . '</voice>');
        $binary = $this->synthesizeOne($ssml);

        Storage::disk('public')->put($cachePath, $binary);

        return $cachePath;
    }

    /**
     * Parsing transkrip "Woman: ... Man: ..." lalu susun jadi SSML lengkap
     * (dengan kalimat pembuka narator otomatis + jeda antar baris).
     */
    protected function buildDialogueSsml(string $transcript, string $voiceWoman, string $voiceMan): string
    {
        $segments = $this->parseTranscript($transcript);

        $intro = $this->escapeXml($this->narratorIntro);
        $inner = '<voice name="' . $voiceMan . '">' . $intro . '<break time="500ms"/></voice>';

        foreach ($segments as $segment) {
            $voice = strtolower($segment['speaker']) === 'woman' ? $voiceWoman : $voiceMan;
            $text = $this->escapeXml($segment['text']);
            $inner .= '<voice name="' . $voice . '">' . $text . '<break time="700ms"/></voice>';
        }

        return $this->wrapSsml($inner);
    }

    /**
     * @return array<int, array{speaker:string, text:string}>
     */
    protected function parseTranscript(string $transcript): array
    {
        $pattern = '/(Woman|Man):\s*/i';
        $parts = preg_split($pattern, $transcript, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $segments = [];
        for ($i = 0; $i < count($parts); $i += 2) {
            $speaker = $parts[$i] ?? null;
            $text = trim($parts[$i + 1] ?? '');

            if ($speaker && $text !== '') {
                $segments[] = ['speaker' => ucfirst(strtolower($speaker)), 'text' => $text];
            }
        }

        if (empty($segments)) {
            $segments[] = ['speaker' => 'Man', 'text' => trim($transcript)];
        }

        return $segments;
    }

    protected function wrapSsml(string $innerXml): string
    {
        return '<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="en-US">' . $innerXml . '</speak>';
    }

    protected function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Panggil Azure Speech REST API untuk SATU SSML, kembalikan binary MP3.
     */
    protected function synthesizeOne(string $ssml): string
    {
        $this->assertKeyPresent();

        $response = Http::withHeaders($this->ttsHeaders())
            ->timeout(30)
            ->withBody($ssml, 'application/ssml+xml')
            ->post($this->endpoint());

        if (! $response->successful()) {
            throw new \RuntimeException('Azure TTS gagal: HTTP ' . $response->status() . ' - ' . $response->body());
        }

        return $response->body();
    }

    /**
     * Panggil Azure Speech REST API untuk BANYAK SSML sekaligus secara
     * paralel (dalam satu batch), pakai Http::pool(). Tidak melempar
     * exception per item gagal — hasilnya dikembalikan per key supaya
     * item lain yang sukses tetap bisa diproses.
     *
     * @param array<int, string> $ssmlByKey
     * @return array<int, array{ok:bool, body?:string, error?:string}>
     */
    protected function synthesizeMany(array $ssmlByKey): array
    {
        $this->assertKeyPresent();

        $keys = array_keys($ssmlByKey);

        $responses = Http::pool(function ($pool) use ($ssmlByKey) {
            $requests = [];
            foreach ($ssmlByKey as $key => $ssml) {
                $requests[$key] = $pool->withHeaders($this->ttsHeaders())
                    ->timeout(30)
                    ->withBody($ssml, 'application/ssml+xml')
                    ->post($this->endpoint());
            }
            return $requests;
        });

        $results = [];
        foreach ($keys as $key) {
            $response = $responses[$key] ?? null;

            if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                $results[$key] = ['ok' => true, 'body' => $response->body()];
            } elseif ($response instanceof \Illuminate\Http\Client\Response) {
                $results[$key] = ['ok' => false, 'error' => 'HTTP ' . $response->status() . ': ' . $response->body()];
            } else {
                $message = $response instanceof \Throwable ? $response->getMessage() : 'Gagal terhubung ke Azure Speech.';
                $results[$key] = ['ok' => false, 'error' => $message];
            }
        }

        return $results;
    }

    protected function ttsHeaders(): array
    {
        return [
            'Ocp-Apim-Subscription-Key' => $this->key,
            'Content-Type' => 'application/ssml+xml',
            'X-Microsoft-OutputFormat' => 'audio-24khz-96kbitrate-mono-mp3',
            'User-Agent' => 'TOEFLPiksiTTS',
        ];
    }

    protected function endpoint(): string
    {
        return "https://{$this->region}.tts.speech.microsoft.com/cognitiveservices/v1";
    }

    protected function assertKeyPresent(): void
    {
        if (blank($this->key)) {
            throw new \RuntimeException('AZURE_SPEECH_KEY kosong atau tidak terbaca. Cek .env Anda.');
        }
    }
}