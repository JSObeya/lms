<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'lms_admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'facilitator']);

        $this->admin = User::factory()->create([
            'email' => 'admin@gregicthub.com',
            'account_status' => 'active',
        ]);
        $this->admin->assignRole('super_admin');

        $this->teacher = User::factory()->create([
            'email' => 'teacher@gregicthub.com',
            'account_status' => 'active',
        ]);
        $this->teacher->assignRole('teacher');
    }

    public function test_admin_can_view_users_list_and_stats(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->has('users.data')
            ->has('stats')
        );
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'first_name' => 'Victor',
            'last_name' => 'Iorver',
            'email' => 'victor.iorver@gregicthub.com',
            'phone' => '+2348011223344',
            'role' => 'teacher',
            'institution' => 'GTC Makurdi',
            'state' => 'Benue',
            'trade_specialization' => 'Solar PV Installation',
            'account_status' => 'active',
            'password' => 'SecurePass2026!',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'victor.iorver@gregicthub.com',
            'name' => 'Victor Iorver',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_created',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->teacher->id), [
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
            'email' => 'updated.teacher@gregicthub.com',
            'phone' => '+2349099887766',
            'role' => 'teacher',
            'institution' => 'GTC Otukpo',
            'state' => 'Benue',
            'trade_specialization' => 'Electrical Maintenance',
            'account_status' => 'active',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $this->teacher->id,
            'name' => 'UpdatedFirst UpdatedLast',
            'email' => 'updated.teacher@gregicthub.com',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_updated',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_user(): void
    {
        // 1. Soft delete
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->teacher->id));
        $response->assertRedirect();

        $this->assertSoftDeleted($this->teacher);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_soft_deleted',
        ]);

        // 2. Restore
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.users.restore', $this->teacher->id));
        $restoreResponse->assertRedirect();

        $this->assertNotSoftDeleted($this->teacher);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'user_restored',
        ]);
    }

    public function test_admin_can_view_system_audit_logs(): void
    {
        AuditLog::record('test_action', User::class, $this->teacher->id, ['test' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/AuditLogs')
            ->has('logs.data')
            ->has('stats')
        );
    }
}
