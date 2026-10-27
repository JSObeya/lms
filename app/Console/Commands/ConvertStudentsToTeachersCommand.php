<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

#[Signature('app:convert-students-to-teachers')]
#[Description('Convert all student accounts and roles to teacher roles')]
class ConvertStudentsToTeachersCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting student to teacher role conversion...');

        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $studentRole = Role::where('name', 'student')->first();

        $convertedCount = 0;

        if ($studentRole) {
            $students = User::role('student')->withTrashed()->get();
            foreach ($students as $student) {
                $student->removeRole('student');
                $student->assignRole($teacherRole);

                AuditLog::record('user_role_converted', User::class, $student->id, [
                    'from_role' => 'student',
                    'to_role' => 'teacher',
                    'reason' => 'Administrative mass conversion from student to teacher',
                ]);

                $convertedCount++;
                $this->line(" - Converted: {$student->name} ({$student->email}) to Teacher");
            }
        }

        // Also assign 'teacher' role to any non-synthetic user without any role
        $usersWithoutRoles = User::where('is_synthetic', false)
            ->doesntHave('roles')
            ->withTrashed()
            ->get();

        foreach ($usersWithoutRoles as $user) {
            $user->assignRole($teacherRole);
            $convertedCount++;
            $this->line(" - Assigned Teacher role to user without role: {$user->name} ({$user->email})");
        }

        $this->info("Complete! Total users converted to Teacher: {$convertedCount}");

        return self::SUCCESS;
    }
}
