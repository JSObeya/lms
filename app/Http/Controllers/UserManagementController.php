<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin();

        $query = User::with('roles')
            ->where('is_synthetic', false);

        // Filter by trashed status: 'only_trashed', 'with_trashed', or default active
        $trashedFilter = $request->input('trashed', 'active_only');
        if ($trashedFilter === 'only_trashed') {
            $query->onlyTrashed();
        } elseif ($trashedFilter === 'with_trashed') {
            $query->withTrashed();
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('account_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('trade_specialization', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();
        $roles = Role::all(['id', 'name']);

        // Comprehensive User Administration Statistics
        $stats = [
            'total_active' => User::where('is_synthetic', false)->count(),
            'total_suspended' => User::where('is_synthetic', false)->where('account_status', 'suspended')->count(),
            'total_trashed' => User::where('is_synthetic', false)->onlyTrashed()->count(),
            'teachers_count' => User::where('is_synthetic', false)->role('teacher')->count(),
            'students_count' => User::where('is_synthetic', false)->role('student')->count(),
            'admins_count' => User::where('is_synthetic', false)->role(['super_admin', 'lms_admin'])->count(),
            'facilitators_count' => User::where('is_synthetic', false)->role('facilitator')->count(),
        ];

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'status', 'trashed']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:100',
            'trade_specialization' => 'nullable|string|max:255',
            'role' => 'required|string|exists:roles,name',
            'account_status' => 'required|in:active,suspended,pending',
            'password' => 'nullable|string|min:8',
        ]);

        $firstName = trim($validated['first_name']);
        $lastName = trim($validated['last_name']);
        $fullName = "{$firstName} {$lastName}";
        $slug = Str::slug($fullName, '_');
        $username = 'tvet_'.$slug.'_'.rand(10, 99);

        $password = ! empty($validated['password'])
            ? Hash::make($validated['password'])
            : Hash::make('TVET_Teacher_2026!');

        $user = User::create([
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'institution' => $validated['institution'] ?? 'Government Technical College, Benue',
            'state' => $validated['state'] ?? 'Benue',
            'trade_specialization' => $validated['trade_specialization'] ?? 'Technical & Vocational Education',
            'account_status' => $validated['account_status'],
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        AuditLog::record('user_created', User::class, $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $validated['role'],
            'institution' => $user->institution,
            'trade' => $user->trade_specialization,
            'status' => $user->account_status,
        ]);

        return back()->with('success', "User account '{$user->name}' created successfully.");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->authorizeAdmin();

        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:100',
            'trade_specialization' => 'nullable|string|max:255',
            'role' => 'required|string|exists:roles,name',
            'account_status' => 'required|in:active,suspended,pending',
            'password' => 'nullable|string|min:8',
        ]);

        $firstName = trim($validated['first_name']);
        $lastName = trim($validated['last_name']);
        $fullName = "{$firstName} {$lastName}";

        $oldAttributes = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
            'status' => $user->account_status,
            'trade' => $user->trade_specialization,
        ];

        $user->name = $fullName;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'] ?? null;
        $user->institution = $validated['institution'] ?? $user->institution;
        $user->state = $validated['state'] ?? $user->state;
        $user->trade_specialization = $validated['trade_specialization'] ?? $user->trade_specialization;
        $user->account_status = $validated['account_status'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        AuditLog::record('user_updated', User::class, $user->id, [
            'previous' => $oldAttributes,
            'updated' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role'],
                'status' => $user->account_status,
                'trade' => $user->trade_specialization,
                'password_reset' => ! empty($validated['password']),
            ],
        ]);

        return back()->with('success', "User account '{$user->name}' updated successfully.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($id === auth()->id()) {
            return back()->with('error', 'Security protection: You cannot delete your own administrator account.');
        }

        $user = User::findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete(); // Soft delete

        AuditLog::record('user_soft_deleted', User::class, $user->id, [
            'name' => $userName,
            'email' => $userEmail,
        ]);

        return back()->with('success', "User '{$userName}' has been moved to trash (soft deleted). The account can be restored at any time.");
    }

    public function restore(int $id): RedirectResponse
    {
        $this->authorizeAdmin();

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        AuditLog::record('user_restored', User::class, $user->id, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return back()->with('success', "User account '{$user->name}' has been restored successfully.");
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if ($id === auth()->id()) {
            return back()->with('error', 'Security protection: You cannot permanently delete your own account.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;

        $user->forceDelete();

        AuditLog::record('user_force_deleted', User::class, $id, [
            'name' => $userName,
            'email' => $userEmail,
        ]);

        return back()->with('success', "User '{$userName}' has been permanently purged from the system.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $oldStatus = $user->account_status;
        $newStatus = $oldStatus === 'active' ? 'suspended' : 'active';
        $user->update(['account_status' => $newStatus]);

        AuditLog::record('user_status_changed', User::class, $user->id, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        return back()->with('success', "User account {$newStatus}.");
    }

    /**
     * Bulk Teacher CSV Import (Supports the 200 IDEAS-TVET Teachers).
     */
    public function bulkImportTeachers(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $importedCount = 0;
        $skippedCount = 0;

        $defaultPassword = Hash::make('TVET_Teacher_2026!');

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[2])) {
                continue;
            }

            $firstName = trim($row[0] ?? '');
            $lastName = trim($row[1] ?? '');
            $email = strtolower(trim($row[2] ?? ''));
            $phone = trim($row[3] ?? '');
            $institution = trim($row[4] ?? 'Government Technical College, Benue');
            $state = trim($row[5] ?? 'Benue');
            $trade = trim($row[6] ?? 'General Technical Education');

            if (User::withTrashed()->where('email', $email)->exists()) {
                $skippedCount++;

                continue;
            }

            $user = User::create([
                'name' => "{$firstName} {$lastName}",
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => 'tvet_'.Str::slug($firstName.'_'.$lastName).'_'.rand(10, 99),
                'email' => $email,
                'phone' => $phone,
                'institution' => $institution,
                'state' => $state,
                'trade_specialization' => $trade,
                'account_status' => 'active',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]);

            $user->syncRoles(['teacher']);
            $importedCount++;
        }

        fclose($handle);

        AuditLog::record('teachers_bulk_imported', User::class, null, [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
        ]);

        return back()->with('success', "Bulk teacher import complete: {$importedCount} accounts created, {$skippedCount} existing accounts skipped.");
    }

    /**
     * Handle bulk actions on selected users (role change, status change, soft delete, restore).
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer',
            'action' => 'required|in:change_role,set_status,move_to_trash,restore',
            'role' => 'nullable|string|exists:roles,name',
            'status' => 'nullable|in:active,suspended,pending',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];
        $affectedCount = 0;

        if ($action === 'change_role') {
            $roleName = $validated['role'] ?? 'teacher';
            $role = Role::findByName($roleName, 'web');
            $users = User::withTrashed()->whereIn('id', $userIds)->get();
            foreach ($users as $user) {
                $user->syncRoles([$role]);
                $affectedCount++;
            }
            AuditLog::record('users_bulk_role_changed', User::class, null, [
                'count' => $affectedCount,
                'new_role' => $roleName,
                'user_ids' => $userIds,
            ]);

            return back()->with('success', "Updated {$affectedCount} user(s) to role '{$roleName}'.");
        }

        if ($action === 'set_status') {
            $status = $validated['status'] ?? 'active';
            $affectedCount = User::withTrashed()->whereIn('id', $userIds)->update(['account_status' => $status]);
            AuditLog::record('users_bulk_status_changed', User::class, null, [
                'count' => $affectedCount,
                'status' => $status,
                'user_ids' => $userIds,
            ]);

            return back()->with('success', "Updated status to '{$status}' for {$affectedCount} user(s).");
        }

        if ($action === 'move_to_trash') {
            $users = User::whereIn('id', $userIds)->where('id', '!=', auth()->id())->get();
            foreach ($users as $user) {
                $user->delete();
                $affectedCount++;
            }
            AuditLog::record('users_bulk_soft_deleted', User::class, null, [
                'count' => $affectedCount,
                'user_ids' => $userIds,
            ]);

            return back()->with('success', "Moved {$affectedCount} user(s) to trash.");
        }

        if ($action === 'restore') {
            $users = User::onlyTrashed()->whereIn('id', $userIds)->get();
            foreach ($users as $user) {
                $user->restore();
                $affectedCount++;
            }
            AuditLog::record('users_bulk_restored', User::class, null, [
                'count' => $affectedCount,
                'user_ids' => $userIds,
            ]);

            return back()->with('success', "Restored {$affectedCount} user(s) from trash.");
        }

        return back();
    }

    protected function authorizeAdmin(): void
    {
        if (! auth()->user()->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Unauthorized access to user administration.');
        }
    }

    protected function authorizeSuperAdmin(): void
    {
        if (! auth()->user()->hasRole('super_admin')) {
            abort(403, 'Only Super Administrators can perform permanent deletions.');
        }
    }
}
