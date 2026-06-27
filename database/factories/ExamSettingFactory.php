<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSetting>
 */
class ExamSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'current_cycle' => 1,
            'is_open' => true,
        ];
    }
}
