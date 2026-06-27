<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id',
        'exam_settings_id',
        'exam_cycle',
        'question_ids',
        'current_question_index',
        'answers',
        'status',
        'started_at',
        'submitted_at',
        'abandoned_at',
    ];

    protected $casts = [
        'question_ids' => 'array',
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'abandoned_at' => 'datetime',
    ];

    // Status constants to avoid magic strings
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_ABANDONED = 'abandoned';

    /**
     * Get the user associated with this exam session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam settings for this session.
     */
    public function examSettings(): BelongsTo
    {
        return $this->belongsTo(ExamSetting::class);
    }

    /**
     * Check if exam is still in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if exam has been submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Mark exam as submitted.
     */
    public function markSubmitted(): self
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
        return $this;
    }

    /**
     * Mark exam as abandoned.
     */
    public function markAbandoned(): self
    {
        $this->update([
            'status' => self::STATUS_ABANDONED,
            'abandoned_at' => now(),
        ]);
        return $this;
    }

    /**
     * Get current question ID.
     */
    public function getCurrentQuestionId(): ?int
    {
        return $this->question_ids[$this->current_question_index] ?? null;
    }

    /**
     * Move to next question.
     */
    public function moveToNextQuestion(): bool
    {
        if ($this->current_question_index < count($this->question_ids) - 1) {
            $this->current_question_index++;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Record answer for a question.
     */
    public function recordAnswer(int $questionId, ?string $answer): self
    {
        $answers = $this->answers ?? [];
        $answers[$questionId] = $answer;
        $this->answers = $answers;
        $this->save();
        return $this;
    }

    /**
     * Get answer for a question.
     */
    public function getAnswer(int $questionId): ?string
    {
        return $this->answers[$questionId] ?? null;
    }
}
