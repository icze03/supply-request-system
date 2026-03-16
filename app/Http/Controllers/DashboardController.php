<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplyRequest;
use App\Models\Supply;
use App\Models\Department;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
    return $this->adminDashboard();
    } elseif ($user->isManager()) {
    return $this->managerDashboard();
    } elseif ($user->isHrManager()) {
    return $this->hrManagerDashboard();
    } else {
    return $this->employeeDashboard();
    }
    }

    // EMPLOYEE DASHBOARD
    private function employeeDashboard()
    {
        $user = auth()->user();

        $data = [
            'totalRequests'    => SupplyRequest::where('user_id', $user->id)->count(),
            'pendingRequests'  => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'approvedRequests' => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'manager_approved')->count(),
            'releasedRequests' => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'admin_released')->count(),
            'recentRequests'   => SupplyRequest::where('user_id', $user->id)
                ->with(['department', 'items'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('employee.dashboard', $data);
    }

    // MANAGER DASHBOARD
    private function managerDashboard()
    {
        $user = auth()->user();

        $data = [
            'pendingCount'    => SupplyRequest::forDepartment($user->department_id)
                ->pending()->count(),
            'approvedToday'   => SupplyRequest::forDepartment($user->department_id)
                ->where('status', 'manager_approved')
                ->whereDate('manager_approved_at', today())
                ->count(),
            'rejectedToday'   => SupplyRequest::forDepartment($user->department_id)
                ->where('status', 'manager_rejected')
                ->whereDate('manager_approved_at', today())
                ->count(),
            'pendingRequests' => SupplyRequest::forDepartment($user->department_id)
                ->pending()
                ->with(['user', 'items.supply'])
                ->latest()
                ->paginate(10),
        ];

        return view('manager.dashboard', $data);
        }
    // HR MANAGER DASHBOARD
    private function hrManagerDashboard()
    {
    $data = [
        'totalUsers'       => \App\Models\User::whereIn('role', ['employee', 'manager'])->count(),
        'totalDepartments' => \App\Models\Department::count(),
        'recentUsers'      => \App\Models\User::with('department')
                                ->whereIn('role', ['employee', 'manager'])
                                ->latest()
                                ->take(5)
                                ->get(),
    ];

    return view('hr_manager.dashboard', $data);
    }

    // ADMIN DASHBOARD
    private function adminDashboard()
    {
        $data = [
            'totalSupplies'   => Supply::count(),
            'activeSupplies'  => Supply::where('is_active', true)->count(),

            // Awaiting manager approval (status = 'pending') — used on blade line 60
            'pendingApproval' => SupplyRequest::where('status', 'pending')->count(),

            // Awaiting admin release (manager already approved)
            'pendingReleases' => SupplyRequest::where('status', 'manager_approved')->count(),

            'releasedToday'   => SupplyRequest::where('status', 'admin_released')
                ->whereDate('admin_released_at', today())
                ->count(),

            // Recent requests pending release (oldest first = most urgent)
            'pendingRequests' => SupplyRequest::where('status', 'manager_approved')
                ->with(['user', 'department', 'items'])
                ->orderBy('created_at', 'asc')
                ->take(10)
                ->get(),

            'departments'     => Department::withCount(['supplyRequests' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])->get(),

            // Low stock alerts
            'lowStockItems'   => Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
                ->where('is_active', true)
                ->orderBy('stock_quantity', 'asc')
                ->limit(10)
                ->get(),
        ];

        

        return view('admin.dashboard', $data);
    }

    public function departmentRequests(Request $request, $id)
    {
        $sort = $request->input('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $paginator = SupplyRequest::with(['user', 'adminReleaser'])
            ->where('department_id', $id)
            ->where('status', 'admin_released')
            ->orderBy('admin_released_at', $sort)
            ->paginate(10);

        return response()->json([
            'success'      => true,
            'total'        => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'from'         => $paginator->firstItem() ?? 0,
            'to'           => $paginator->lastItem()  ?? 0,
            'requests'     => $paginator->map(fn($req) => [
                'id'            => $req->id,
                'sr_number'     => $req->sr_number,
                'serial_number' => $req->serial_number,
                'ro_number'     => $req->ro_number,
                'request_type'  => $req->request_type,
                'budget_type'   => $req->budget_type,
                'user_name'     => $req->user->name,
                'user_email'    => $req->user->email,
                'released_at'   => $req->admin_released_at->format('M d, Y'),
                'released_by'   => $req->adminReleaser->name ?? null,
            ]),
        ]);
    }
}