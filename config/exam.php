<?php

/**
 * TOEFL Exam Configuration
 * Defines question distribution, scoring, and session settings
 */

return [
    /**
     * Exam Question Distribution
     * Total questions and per-section targets for exam sessions
     */
    'exam' => [
        'total_questions' => 50,
        'sections' => [
            'listening' => 17,
            'structure' => 17,
            'reading' => 16,
        ],
        'section_order' => ['listening', 'structure', 'reading'],
    ],

    /**
     * Practice Session Configuration
     * Settings for practice mode (currently uses all available questions)
     */
    'practice' => [
        'section_order' => ['listening', 'structure', 'reading'],
    ],

    /**
     * Scoring Configuration
     * TOEFL score conversion formulas
     */
    'scoring' => [
        'base_score' => 20,
        'multiplier' => 1.2,
        'total_formula' => 'round((listening + structure + reading) * 10 / 3)',
    ],

    /**
     * Review Quota Configuration
     * Limits for AI-generated practice reviews
     */
    'review' => [
        'daily_quota' => 10,
        'cache_ttl_days' => 14,
    ],
];
