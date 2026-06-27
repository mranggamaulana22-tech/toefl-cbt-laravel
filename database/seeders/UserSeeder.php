<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed default users for admin and student login.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@piksi.test'],
            [
                'name' => 'Admin Piksi',
                'npm' => 'ADM001',
                'class' => 'STAFF',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@piksi.test'],
            [
                'name' => 'Student Piksi',
                'npm' => '22010001',
                'class' => 'TI-1A',
                'role' => 'student',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
