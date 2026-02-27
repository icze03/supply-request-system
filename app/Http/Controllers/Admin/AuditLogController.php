<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'department'])->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);

        // Get unique actions for filter
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = User::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.audit-logs.index', compact('logs', 'actions', 'users', 'departments'));
    }

    /**
     * Show detailed audit log
     */
    public function show($id)
    {
        $log = AuditLog::with(['user', 'department'])->findOrFail($id);

        return view('admin.audit-logs.show', compact('log'));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        $query = AuditLog::with(['user', 'department'])->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Date/Time',
                'Action',
                'User',
                'Department',
                'Description',
                'IP Address',
                'Model Type',
                'Model ID'
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->action,
                    $log->user ? $log->user->name : 'System',
                    $log->department ? $log->department->name : 'N/A',
                    $log->description,
                    $log->ip_address,
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->model_id ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'this_week_logs' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_logs' => AuditLog::whereMonth('created_at', now()->month)->count(),
            'actions_by_type' => AuditLog::select('action', \DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'top_users' => AuditLog::select('user_id', \DB::raw('count(*) as count'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->with('user')
                ->get(),
        ];

        return response()->json($stats);
    }
}