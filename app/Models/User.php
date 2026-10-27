<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'institution',
        'state',
        'trade_specialization',
        'account_status',
        'is_synthetic',
        'sandbox_owner_id',
        'last_login_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_synthetic' => 'boolean',
        ];
    }

    /**
     * Courses created/owned by this user.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    /**
     * Course enrollments.
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'user_id');
    }

    /**
     * Courses enrolled in as a student/observer.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
            ->withPivot('role', 'status', 'progress_percentage', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Submissions made by this student.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'user_id');
    }

    /**
     * Teacher's synthetic practice learners (sandbox mode).
     */
    public function practiceLearners(): HasMany
    {
        return $this->hasMany(User::class, 'sandbox_owner_id');
    }

    /**
     * Owner teacher if this user is a synthetic practice learner.
     */
    public function sandboxOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sandbox_owner_id');
    }

    /**
     * Day 8 TVET Practical Wizard progress.
     */
    public function day8Progress(): HasOne
    {
        return $this->hasOne(Day8PracticeProgress::class, 'user_id');
    }

    /**
     * Teacher portfolio.
     */
    public function portfolio(): HasOne
    {
        return $this->hasOne(TeacherPortfolio::class, 'user_id');
    }

    /**
     * Rubrics created by this teacher.
     */
    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class, 'user_id');
    }

    /**
     * Role checks.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isLmsAdmin(): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('lms_admin');
    }

    public function isFacilitator(): bool
    {
        return $this->hasRole('facilitator');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }
}
