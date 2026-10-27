<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Super Admin and LMS Admin have override capabilities where authorized.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        // Owner/creator
        if ($course->user_id === $user->id) {
            return true;
        }

        // Admin
        if ($user->hasRole(['super_admin', 'lms_admin'])) {
            return true;
        }

        // Facilitator
        if ($user->hasRole('facilitator')) {
            return true;
        }

        // Published courses are viewable
        if ($course->status === 'published') {
            return true;
        }

        // Enrolled student
        return $course->enrollments()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['teacher', 'lms_admin', 'super_admin']);
    }

    public function update(User $user, Course $course): bool
    {
        // Teachers cannot edit other teachers' courses without explicit authorization
        if ($user->hasRole('teacher')) {
            return $course->user_id === $user->id;
        }

        return $user->hasRole(['lms_admin', 'super_admin']);
    }

    public function delete(User $user, Course $course): bool
    {
        if ($user->hasRole('teacher')) {
            return $course->user_id === $user->id;
        }

        return $user->hasRole('super_admin');
    }

    public function grade(User $user, Course $course): bool
    {
        // Teacher owning the course
        if ($course->user_id === $user->id) {
            return true;
        }

        // Assigned facilitator or admin
        return $user->hasRole(['facilitator', 'lms_admin', 'super_admin']);
    }

    public function export(User $user, Course $course): bool
    {
        if ($course->user_id === $user->id) {
            return true;
        }

        return $user->hasRole(['facilitator', 'lms_admin', 'super_admin']);
    }
}
