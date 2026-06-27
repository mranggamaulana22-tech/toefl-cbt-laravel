<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    /**
     * Determine if the user can view the result.
     */
    public function view(User $user, Result $result): bool
    {
        return $user->id === $result->user_id;
    }

    /**
     * Determine if the user can update the result.
     */
    public function update(User $user, Result $result): bool
    {
        return $user->id === $result->user_id;
    }
}
