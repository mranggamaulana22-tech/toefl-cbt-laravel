<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Abstract base class for OpenRouter API integration
 * Eliminates code duplication between OpenRouterService and PracticeReviewService
 */
abstract class BaseOpenRouterService
{
    protected ?string $apiKey;
    protected string $model;
    protected ?string $fallbackModel;
    protected ?string $fallbackModel2;
    protected int $timeoutSeconds;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';
    protected ?string $lastError = null;
    protected ?string $lastUsedModel = null;

    public function __construct()
    {
        $rawKey = trim((string) config('services.openrouter.key', ''));

        // Accept either raw key or "Bearer <key>" input from .env.
        $this->apiKey = preg_replace('/^Bearer\s+/i', '', $rawKey) ?: null;
        $this->model = (string) config('services.openrouter.model', 'openai/gpt-oss-20b:free');
        $this->fallbackModel = config('services.openrouter.fallback_model');
        $this->fallbackModel2 = config('services.openrouter.fallback_model_2');
        $this->timeoutSeconds = (int) config('services.openrouter.timeout', 60);
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getLastUsedModel(): ?string
    {
        return $this->lastUsedModel;
    }

    /**
     * Get system message for this service
     * Subclasses should override to provide specific system instructions
     */
    protected function getSystemMessage(): string
    {
        return 'Anda adalah asisten AI yang membantu dengan pendidikan TOEFL.';
    }

    /**
     * Get model list to try (primary, fallback1, fallback2)
     */
    protected function getModelsToTry(): array
    {
        return array_values(array_unique(array_filter([
            $this->model,
            $this->fallbackModel,
            $this->fallbackModel2,
        ])));
    }

    /**
     * Send request to OpenRouter API
     */
    protected function sendRequest(string $model, string $prompt, int $maxTokens, bool $includeSystem = true)
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        if ($includeSystem) {
            array_unshift($messages, [
                'role' => 'system',
                'content' => $this->getSystemMessage(),
            ]);
        }

        return Http::withToken($this->apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name', 'TOEFL PIKSI'),
                'Content-Type' => 'application/json',
            ])
            ->connectTimeout(10)
            ->timeout($this->timeoutSeconds)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $this->getTemperature(),
                'max_tokens' => $maxTokens,
            ]);
    }

    /**
     * Get temperature setting for this service
     * Subclasses can override for different values
     */
    protected function getTemperature(): float
    {
        return 0.35;
    }

    /**
     * Check if we should try fallback model based on response status/body
     */
    protected function shouldTryFallback(int $status, string $body): bool
    {
        if (in_array($status, [404, 408, 429, 500, 502, 503, 504], true)) {
            return true;
        }

        if ($status === 400 && (
            str_contains(strtolower($body), 'rate-limit') ||
            str_contains(strtolower($body), 'developer instruction is not enabled') ||
            str_contains(strtolower($body), 'invalid_argument')
        )) {
            return true;
        }

        return false;
    }

    /**
     * Check if error is due to developer instruction not enabled
     */
    protected function isDeveloperInstructionError(int $status, string $body): bool
    {
        if ($status !== 400) {
            return false;
        }

        return str_contains(strtolower($body), 'developer instruction is not enabled');
    }

    /**
     * Build models to try and handle request/response with retry logic
     */
    protected function executeWithFallback(
        string $prompt,
        callable $maxTokensCallback,
        callable $processResponseCallback
    ) {
        if (blank($this->apiKey)) {
            $this->lastError = 'OPENROUTER_API_KEY kosong atau tidak terbaca.';
            \Log::warning('OpenRouter API key is missing.');
            return null;
        }

        $modelsToTry = $this->getModelsToTry();
        $lastResponse = null;
        $lastModelTried = null;

        foreach ($modelsToTry as $index => $modelToTry) {
            $maxTokens = $maxTokensCallback($index);

            try {
                $response = $this->sendRequest($modelToTry, $prompt, $maxTokens);
            } catch (ConnectionException $e) {
                $lastModelTried = $modelToTry;

                if (isset($modelsToTry[$index + 1])) {
                    continue;
                }

                $this->lastError = 'OpenRouter timeout. Coba lagi sebentar lagi atau ganti model yang lebih ringan.';
                \Log::error('OpenRouter timeout', [
                    'model' => $lastModelTried,
                    'timeout_seconds' => $this->timeoutSeconds,
                    'message' => $e->getMessage(),
                ]);

                return null;
            }

            if ($response->successful()) {
                $this->lastUsedModel = $modelToTry;
                $content = $response->json('choices.0.message.content');
                return $processResponseCallback($content ?: null, $modelToTry);
            }

            if ($this->isDeveloperInstructionError($response->status(), $response->body())) {
                try {
                    $retryResponse = $this->sendRequest($modelToTry, $prompt, $maxTokens, false);

                    if ($retryResponse->successful()) {
                        $this->lastUsedModel = $modelToTry;
                        $content = $retryResponse->json('choices.0.message.content');
                        return $processResponseCallback($content ?: null, $modelToTry);
                    }

                    $response = $retryResponse;
                } catch (ConnectionException $e) {
                    // Continue to next model
                    continue;
                }
            }

            $lastResponse = $response;
            $lastModelTried = $modelToTry;

            $shouldTryNext = $this->shouldTryFallback($response->status(), $response->body());
            $hasNextModel = isset($modelsToTry[$index + 1]);

            if (!($shouldTryNext && $hasNextModel)) {
                break;
            }
        }

        if ($lastResponse) {
            $status = $lastResponse->status();
            $body = $lastResponse->body();
            $this->lastError = "OpenRouter HTTP {$status}: {$body}";

            \Log::error('OpenRouter non-success response', [
                'status' => $status,
                'model' => $lastModelTried ?: $this->model,
                'body' => $body,
            ]);
        }

        return null;
    }
}
