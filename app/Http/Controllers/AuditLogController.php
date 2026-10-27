<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin();

        $query = AuditLog::with('user:id,name,email')
            ->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', 'like', "%{$request->entity_type}%");
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        // Distinct actions for filtering
        $availableActions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Audit Summary Stats
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_events' => AuditLog::whereDate('created_at', today())->count(),
            'security_actions' => AuditLog::whereIn('action', [
                'user_created',
                'user_updated',
                'user_soft_deleted',
                'user_restored',
                'user_force_deleted',
                'user_status_changed',
                'user_logged_in',
                'user_logged_out',
            ])->count(),
            'course_rubric_actions' => AuditLog::whereIn('action', [
                'course_created',
                'course_updated',
                'course_deleted',
                'course_restored',
                'rubric_created',
                'rubric_deleted',
                'rubric_restored',
                'assignment_created',
                'assignment_updated',
                'assignment_deleted',
                'grade_released',
            ])->count(),
        ];

        return Inertia::render('Admin/AuditLogs', [
            'logs' => $logs,
            'availableActions' => $availableActions,
            'stats' => $stats,
            'filters' => $request->only(['action', 'entity_type', 'user_id', 'date_from', 'date_to', 'search']),
        ]);
    }

    public function clearOld(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $days = (int) $request->input('days', 90);
        if ($days < 30) {
            $days = 30; // Safety floor
        }

        $cutoff = now()->subDays($days);
        $deletedCount = AuditLog::where('created_at', '<', $cutoff)->delete();

        AuditLog::record('audit_logs_purged', AuditLog::class, null, [
            'days_threshold' => $days,
            'purged_count' => $deletedCount,
            'cutoff_date' => $cutoff->toDateTimeString(),
        ]);

        return back()->with('success', "Audit trail maintenance complete: {$deletedCount} logs older than {$days} days purged.");
    }

    protected function authorizeAdmin(): void
    {
        if (! auth()->user()->hasRole(['super_admin', 'lms_admin'])) {
            abort(403, 'Unauthorized access to system audit logs.');
        }
    }

    protected function authorizeSuperAdmin(): void
    {
        if (! auth()->user()->hasRole('super_admin')) {
            abort(403, 'Only Super Administrators can purge historical audit logs.');
        }
    }
}
