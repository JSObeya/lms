<?php

namespace App\Policies;

use App\Models\CompetencyLesson;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetencyLessonPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['teacher', 'facilitator', 'lms_admin', 'super_admin']);
    }

    public function view(User $user, CompetencyLesson $lesson): bool
    {
        // System templates or published lessons are visible to all teachers/facilitators
        if ($lesson->is_template || $lesson->status === 'published') {
            return true;
        }

        // Author
        if ($lesson->user_id === $user->id) {
            return true;
        }

        // Facilitators and Admins
        if ($user->hasRole(['facilitator', 'lms_admin'])) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['teacher', 'lms_admin', 'super_admin']);
    }

    public function update(User $user, CompetencyLesson $lesson): bool
    {
        // System templates cannot be directly overwritten by ordinary teachers
        if ($lesson->is_template && ! $user->hasRole(['super_admin', 'lms_admin'])) {
            return false;
        }

        // Only the owner teacher or admin can modify
        if ($lesson->user_id === $user->id) {
            return true;
        }

        return $user->hasRole(['super_admin', 'lms_admin']);
    }

    public function delete(User $user, CompetencyLesson $lesson): bool
    {
        if ($lesson->is_template) {
            return false;
        }

        return $lesson->user_id === $user->id || $user->hasRole(['super_admin', 'lms_admin']);
    }

    public function duplicate(User $user, CompetencyLesson $lesson): bool
    {
        return $user->hasRole(['teacher', 'lms_admin', 'super_admin']);
    }

    public function assess(User $user, CompetencyLesson $lesson): bool
    {
        return $user->hasRole(['facilitator', 'super_admin', 'lms_admin']);
    }
}
