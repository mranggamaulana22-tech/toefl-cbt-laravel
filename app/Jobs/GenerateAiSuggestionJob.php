<?php

namespace App\Jobs;

use App\Models\PracticeResult;
use App\Models\Result;
use App\Services\AiSuggestionParser;
use App\Services\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateAiSuggestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public string $type,
        public int $recordId,
        public int $userId,
        public array $suggestionData
    ) {
    }

    public function handle(OpenRouterService $openRouterService, AiSuggestionParser $aiSuggestionParser): void
    {
        $lockKey = $this->queueLockKey();

        $record = $this->resolveRecord();

        if (!$record || (int) $record->user_id !== $this->userId) {
            Cache::forget($lockKey);
            return;
        }

        if (!empty($record->ai_suggestion)) {
            if ($record->ai_status !== 'done') {
                $parsed = $aiSuggestionParser->parse($record->ai_suggestion);

                $record->update([
                    'ai_status' => 'done',
                    'ai_error' => null,
                    'ai_parsed_json' => $parsed,
                    'ai_parser_version' => $aiSuggestionParser->version(),
                    'ai_completed_at' => $record->ai_completed_at ?: now(),
                ]);
            }

            Cache::forget($lockKey);
            return;
        }

        $record->update([
            'ai_status' => 'processing',
            'ai_error' => null,
        ]);

        $suggestion = $openRouterService->generateSuggestion($this->suggestionData);

        if (!$suggestion) {
            $record->update([
                'ai_status' => 'failed',
                'ai_error' => $openRouterService->getLastError(),
                'ai_parsed_json' => null,
                'ai_completed_at' => now(),
            ]);

            Cache::forget($lockKey);
            return;
        }

        $parsed = $aiSuggestionParser->parse($suggestion);

        $record->update([
            'ai_suggestion' => $suggestion,
            'ai_generated_at' => now(),
            'ai_model_used' => $openRouterService->getLastUsedModel() ?: null,
            'ai_parsed_json' => $parsed,
            'ai_parser_version' => $aiSuggestionParser->version(),
            'ai_status' => 'done',
            'ai_error' => null,
            'ai_completed_at' => now(),
        ]);

        Cache::forget($lockKey);
    }

    protected function resolveRecord(): Result|PracticeResult|null
    {
        if ($this->type === 'exam') {
            return Result::query()->find($this->recordId);
        }

        if ($this->type === 'practice') {
            return PracticeResult::query()->find($this->recordId);
        }

        return null;
    }

    protected function queueLockKey(): string
    {
        return "ai-suggestion:{$this->type}:{$this->recordId}";
    }
}
