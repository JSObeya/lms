<?php

namespace App\Policies;

use App\Models\Rubric;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RubricPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, Rubric $rubric): bool
    {
        return true;
    }

    public function update(User $user, Rubric $rubric): bool
    {
        if ($user->hasRole('teacher')) {
            return $rubric->user_id === $user->id;
        }

        return $user->hasRole('lms_admin');
    }

    public function delete(User $user, Rubric $rubric): bool
    {
        if ($user->hasRole('teacher')) {
            return $rubric->user_id === $user->id;
        }

        return $user->hasRole('lms_admin');
    }
}
