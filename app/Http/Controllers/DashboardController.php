<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplyRequest;
use App\Models\Supply;
use App\Models\Department;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Redirect based on role
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        } else {
            return $this->employeeDashboard();
        }
    }

    // EMPLOYEE DASHBOARD
    private function employeeDashboard()
    {
        $user = auth()->user();
        
        $data = [
            'totalRequests' => SupplyRequest::where('user_id', $user->id)->count(),
            'pendingRequests' => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'approvedRequests' => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'manager_approved')->count(),
            'releasedRequests' => SupplyRequest::where('user_id', $user->id)
                ->where('status', 'admin_released')->count(),
            'recentRequests' => SupplyRequest::where('user_id', $user->id)
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
            'pendingCount' => SupplyRequest::forDepartment($user->department_id)
                ->pending()->count(),
            'approvedToday' => SupplyRequest::forDepartment($user->department_id)
                ->where('status', 'manager_approved')
                ->whereDate('manager_approved_at', today())
                ->count(),
            'rejectedToday' => SupplyRequest::forDepartment($user->department_id)
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

    // ADMIN DASHBOARD
    private function adminDashboard()
    {
        $data = [
            'totalSupplies' => Supply::count(),
            'activeSupplies' => Supply::where('is_active', true)->count(),
            'pendingApproval' => SupplyRequest::where('status', 'manager_approved')->count(),
            'releasedToday' => SupplyRequest::where('status', 'admin_released')
                ->whereDate('admin_released_at', today())
                ->count(),
            'pendingRequests' => SupplyRequest::where('status', 'manager_approved')
                ->with(['user', 'department', 'items'])
                ->latest()
                ->take(10)
                ->get(),
            'departments' => Department::withCount(['supplyRequests' => function($query) {
                $query->where('status', 'admin_released');
            }])->get(),
        ];

        return view('admin.dashboard', $data);
    }
}