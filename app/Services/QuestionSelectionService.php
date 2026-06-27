<?php

namespace App\Services;

use App\Models\PracticeQuestion;
use App\Models\Question;
use Illuminate\Support\Collection;

/**
 * Service for question selection and ordering in exams and practices
 * Consolidates logic from ExamController
 */
class QuestionSelectionService
{
    /**
     * Select and order question IDs for exam
     * Returns configured number of questions distributed across sections
     * Configuration: config/exam.php
     */
    public function generateOrderedExamQuestionIds(): array
    {
        $selectedIds = [];
        $examConfig = config('exam.exam');
        $sectionTargets = $examConfig['sections'];
        $sectionOrder = $examConfig['section_order'];
        $examTotal = $examConfig['total_questions'];

        // First, get target count for each section
        foreach ($sectionOrder as $section) {
            $target = $sectionTargets[$section] ?? 0;

            if ($target <= 0) {
                continue;
            }

            $sectionIds = Question::where('category', $section)
                ->inRandomOrder()
                ->limit($target)
                ->pluck('id')
                ->all();

            $selectedIds = array_merge($selectedIds, $sectionIds);
        }

        // If we don't have enough questions, fill remaining slots from any section
        $remaining = $examTotal - count($selectedIds);

        if ($remaining > 0) {
            foreach ($sectionOrder as $section) {
                if ($remaining <= 0) {
                    break;
                }

                $extraIds = Question::where('category', $section)
                    ->whereNotIn('id', $selectedIds)
                    ->inRandomOrder()
                    ->limit($remaining)
                    ->pluck('id')
                    ->all();

                if (!empty($extraIds)) {
                    $selectedIds = array_merge($selectedIds, $extraIds);
                    $remaining = $examTotal - count($selectedIds);
                }
            }
        }

        return array_values(array_slice($selectedIds, 0, $examTotal));
    }

    /**
     * Select and order question IDs for practice
     * Returns all practice questions distributed by section (from config order)
     */
    public function generateOrderedPracticeQuestionIds(): array
    {
        $questionIds = [];
        $sectionOrder = config('exam.practice.section_order', ['listening', 'structure', 'reading']);

        foreach ($sectionOrder as $section) {
            $sectionIds = PracticeQuestion::where('category', $section)
                ->inRandomOrder()
                ->pluck('id')
                ->all();

            if (!empty($sectionIds)) {
                $questionIds = array_merge($questionIds, $sectionIds);
            }
        }

        // If we don't have questions in sections, get all
        if (empty($questionIds)) {
            $questionIds = PracticeQuestion::inRandomOrder()->pluck('id')->all();
        }

        return array_values($questionIds);
    }

    /**
     * Order a collection of models by the original question ID sequence.
     */
    public function orderByIds(Collection $models, array $questionIds): Collection
    {
        $indexMap = array_flip($questionIds);

        return $models
            ->sortBy(fn ($model) => $indexMap[$model->id] ?? PHP_INT_MAX)
            ->values();
    }
}
