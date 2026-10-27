<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($course->user_id === $user->id) {
            return true;
        }

        if ($user->hasRole(['lms_admin', 'facilitator'])) {
            return true;
        }

        // Student must be enrolled
        return $course->enrollments()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('teacher')) {
            return $assignment->course->user_id === $user->id;
        }

        return $user->hasRole('lms_admin');
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole('teacher')) {
            return $assignment->course->user_id === $user->id;
        }

        return $user->hasRole('lms_admin');
    }

    public function submit(User $user, Assignment $assignment): bool
    {
        // Enrolled student or synthetic practice student
        return $assignment->course->enrollments()->where('user_id', $user->id)->exists();
    }

    public function grade(User $user, Assignment $assignment): bool
    {
        if ($assignment->course->user_id === $user->id) {
            return true;
        }

        return $user->hasRole(['facilitator', 'lms_admin']);
    }
}
