<?php

namespace App\Services;

class AiSuggestionParser
{
    public const VERSION = 'v1';

    public function version(): string
    {
        return self::VERSION;
    }

    public function parse(?string $raw): array
    {
        $text = trim((string) $raw);

        if ($text === '') {
            return $this->emptyPayload();
        }

        $cleaned = $this->cleanText($text);
        $decoded = $this->decodeJsonPayload($cleaned);

        if (is_array($decoded)) {
            return $this->normalize($decoded);
        }

        return $this->normalize([
            'strengths' => $this->extractLabeledField($cleaned, 'KEKUATAN'),
            'weaknesses' => $this->extractLabeledField($cleaned, 'KELEMAHAN'),
            'error_pattern' => $this->extractLabeledField($cleaned, 'POLA_KESALAHAN'),
            'motivation' => $this->extractLabeledField($cleaned, 'MOTIVASI'),
            'priority_tip' => $this->extractLabeledField($cleaned, 'PRIORITY'),
            'warning' => $this->extractLabeledField($cleaned, 'WARNING'),
            'plan' => $this->extractPlanFromLabels($cleaned),
        ]);
    }

    protected function emptyPayload(): array
    {
        return [
            'strengths' => '',
            'weaknesses' => '',
            'error_pattern' => '',
            'motivation' => '',
            'priority_tip' => '',
            'warning' => '',
            'scores' => [],
            'time_management' => [],
            'plan' => [],
        ];
    }

    protected function cleanText(string $text): string
    {
        return trim((string) preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text));
    }

    protected function decodeJsonPayload(string $text): ?array
    {
        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        $slice = substr($text, $start, $end - $start + 1);
        $decoded = json_decode($slice, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $repaired = preg_replace('/,\s*([}\]])/', '$1', $slice);
        $decoded = json_decode((string) $repaired, true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function extractLabeledField(string $text, string $label): string
    {
        $labels = ['KEKUATAN', 'KELEMAHAN', 'POLA_KESALAHAN', 'MOTIVASI', 'PRIORITY', 'WARNING', 'RENCANA_3_HARI', 'RENCANA_7_HARI'];
        $others = array_filter($labels, fn ($item) => $item !== $label);
        $next = implode('|', array_map(fn ($item) => preg_quote($item, '/'), $others));

        $pattern = '/'.preg_quote($label, '/').'\s*:\s*([\s\S]*?)(?=\n(?:'.$next.')\s*:|$)/i';
        if (!preg_match($pattern, $text, $matches)) {
            return '';
        }

        return trim((string) ($matches[1] ?? ''));
    }

    protected function extractPlanFromLabels(string $text): array
    {
        $planBlock = $this->extractLabeledField($text, 'RENCANA_3_HARI');
        if ($planBlock === '') {
            $planBlock = $this->extractLabeledField($text, 'RENCANA_7_HARI');
        }

        if ($planBlock === '') {
            return [];
        }

        $lines = preg_split('/\r?\n/', $planBlock) ?: [];
        $rows = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if (!preg_match('/^hari\s*(\d+)\s*\|/i', $line, $dayMatch)) {
                continue;
            }

            preg_match('/fokus\s*:\s*([^|]+)/i', $line, $focusMatch);
            preg_match('/durasi\s*:\s*([^|]+)/i', $line, $durationMatch);
            preg_match('/tugas\s*:\s*(.+)$/i', $line, $taskMatch);

            $day = (int) ($dayMatch[1] ?? (count($rows) + 1));
            $focus = trim((string) ($focusMatch[1] ?? 'Latihan'));
            $duration = trim((string) ($durationMatch[1] ?? '30 mnt'));
            $task = trim((string) ($taskMatch[1] ?? $line));

            $rows[] = [
                'day' => $day,
                'title' => $task,
                'desc' => $task.' ('.$duration.')',
                'tags' => [$focus],
            ];
        }

        return array_slice($rows, 0, 3);
    }

    protected function normalize(array $data): array
    {
        $strengths = trim((string) ($data['strengths'] ?? ''));
        $weaknesses = trim((string) ($data['weaknesses'] ?? ''));
        $errorPattern = trim((string) ($data['error_pattern'] ?? ''));
        $motivation = trim((string) (($data['motivation'] ?? '') ?: ($data['quick_win'] ?? '')));
        $priority = trim((string) ($data['priority_tip'] ?? ''));
        $warning = trim((string) ($data['warning'] ?? ''));

        $planSource = [];
        if (!empty($data['plan']) && is_array($data['plan'])) {
            $planSource = $data['plan'];
        } elseif (!empty($data['day_plan']) && is_array($data['day_plan'])) {
            $planSource = $data['day_plan'];
        }

        $plan = [];
        foreach (array_slice($planSource, 0, 3) as $idx => $item) {
            if (!is_array($item)) {
                continue;
            }

            $day = (int) ($item['day'] ?? ($idx + 1));
            $title = trim((string) ($item['title'] ?? $item['task'] ?? ('Hari '.$day)));
            $desc = trim((string) ($item['desc'] ?? (($item['task'] ?? $title).' ('.($item['duration'] ?? '30 mnt').')')));

            $tags = $item['tags'] ?? ($item['focus'] ?? 'Latihan');
            if (!is_array($tags)) {
                $tags = [trim((string) $tags)];
            }

            $plan[] = [
                'day' => $day,
                'title' => $title,
                'desc' => $desc,
                'tags' => array_values(array_filter(array_map('trim', $tags))),
            ];
        }

        return [
            'strengths' => $strengths,
            'weaknesses' => $weaknesses,
            'error_pattern' => $errorPattern,
            'motivation' => $motivation,
            'priority_tip' => $priority,
            'warning' => $warning,
            'scores' => is_array($data['scores'] ?? null) ? $data['scores'] : [],
            'time_management' => is_array($data['time_management'] ?? null) ? $data['time_management'] : [],
            'plan' => $plan,
        ];
    }
}
