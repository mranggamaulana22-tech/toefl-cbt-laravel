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
    public function getExamRequirements(): array
    {
        return config('exam.exam');
    }

    public function getExamReadinessReport(): array
    {
        $examConfig = $this->getExamRequirements();
        $sectionTargets = $examConfig['sections'];
        $sectionOrder = $examConfig['section_order'];

        $sections = [];
        $canStart = true;

        foreach ($sectionOrder as $section) {
            $required = (int) ($sectionTargets[$section] ?? 0);
            $available = (int) Question::where('category', $section)->count();
            $shortage = max(0, $required - $available);

            if ($shortage > 0) {
                $canStart = false;
            }

            $sections[$section] = [
                'required' => $required,
                'available' => $available,
                'shortage' => $shortage,
            ];
        }

        return [
            'can_start' => $canStart,
            'required_total' => (int) $examConfig['total_questions'],
            'available_total' => (int) Question::count(),
            'sections' => $sections,
        ];
    }

    /**
     * Select and order question IDs for exam
     * Returns configured number of questions distributed across sections
     * Configuration: config/exam.php
     */
    public function generateOrderedExamQuestionIds(): array
    {
        $report = $this->getExamReadinessReport();

        if (! $report['can_start']) {
            return [];
        }

        $selectedIds = [];
        $examConfig = $this->getExamRequirements();
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

        return array_values(array_slice($selectedIds, 0, $examTotal));
    }

    /**
     * Select and order question IDs for practice
    * Returns all practice questions distributed by section order from config
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
