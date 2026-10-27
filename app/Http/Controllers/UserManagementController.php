<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('trade_specialization', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $newStatus = $user->account_status === 'active' ? 'suspended' : 'active';
        $user->update(['account_status' => $newStatus]);

        AuditLog::record('user_status_changed', User::class, $user->id, [
            'old_status' => $user->account_status,
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

        $header = fgetcsv($handle); // First Name, Last Name, Email, Phone, Institution, State, Trade Specialization
        $importedCount = 0;
        $skippedCount = 0;
        $errors = [];

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

            // Duplicate detection
            if (User::where('email', $email)->exists()) {
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

    protected function authorizeAdmin(): void
    {
        if (! auth()->user()->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Unauthorized access to user administration.');
        }
    }
}
