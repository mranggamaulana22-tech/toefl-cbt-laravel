<?php

namespace App\Policies;

use App\Models\PracticeResult;
use App\Models\User;

class PracticeResultPolicy
{
    /**
     * Determine if the user can view the result.
     */
    public function view(User $user, PracticeResult $practiceResult): bool
    {
        return $user->id === $practiceResult->user_id;
    }

    /**
     * Determine if the user can update the result.
     */
    public function update(User $user, PracticeResult $practiceResult): bool
    {
        return $user->id === $practiceResult->user_id;
    }
}
