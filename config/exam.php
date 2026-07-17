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
        'total_questions' => 140,
        'sections' => [
            'listening' => 50,
            'structure' => 40,
            'reading' => 50,
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
     * Review Quota Configuration
     * Limits for AI-generated practice reviews
     */
    'review' => [
        'daily_quota' => 10,
        'cache_ttl_days' => 14,
    ],
];
