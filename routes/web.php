<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\CatalogController;
use App\Http\Controllers\Employee\RequestController;
use App\Http\Controllers\Manager\ApprovalController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\SuperAdmin\RolePermissionController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard — role-based redirect
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================================================================
    // SUPER ADMIN ROUTES
    // =========================================================================
    Route::middleware('super_admin')
        ->prefix('super-admin')
        ->name('super_admin.')
        ->group(function () {

            Route::get('/dashboard',
                [RolePermissionController::class, 'dashboard']
            )->name('dashboard');

            Route::get('/role-permissions',
                [RolePermissionController::class, 'index']
            )->name('role_permissions.index');

            Route::post('/role-permissions',
                [RolePermissionController::class, 'update']
            )->name('role_permissions.update');
        });

    // =========================================================================
    // EMPLOYEE ROUTES
    // =========================================================================
    Route::middleware('employee')->prefix('employee')->name('employee.')->group(function () {

        Route::get('/catalog',          [CatalogController::class, 'index'])->name('catalog')
            ->middleware('permission:catalog');
        Route::get('/catalog/supplies', [CatalogController::class, 'getSupplies'])->name('catalog.supplies');
        Route::get('/catalog/{id}',     [CatalogController::class, 'show'])->name('catalog.show');

        Route::get('/requests',         [RequestController::class, 'index'])->name('requests.index')
            ->middleware('permission:my_requests');
        Route::get('/requests/{id}',    [RequestController::class, 'show'])->name('requests.show')
            ->middleware('permission:my_requests');
        Route::post('/requests',        [RequestController::class, 'store'])->name('requests.store');
        Route::post('/requests/special',[RequestController::class, 'storeSpecial'])->name('requests.special');
        Route::delete('/requests/{id}', [RequestController::class, 'cancel'])->name('requests.cancel');
        Route::post('/requests/{id}/return',   [RequestController::class, 'submitReturn'])->name('requests.return');
        Route::get('/requests/{id}/voucher',   [RequestController::class, 'voucher'])->name('requests.voucher');
    });

    // =========================================================================
    // MANAGER ROUTES
    // =========================================================================
    Route::middleware('manager')->prefix('manager')->name('manager.')->group(function () {

        Route::get('/approvals',        [ApprovalController::class, 'index'])->name('approvals.index')
            ->middleware('permission:approvals');
        Route::get('/approvals/{id}',   [ApprovalController::class, 'show'])->name('approvals.show')
            ->middleware('permission:approvals');
        Route::post('/approvals/{id}/approve',         [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject',          [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('/approvals/{id}/update-quantity', [ApprovalController::class, 'updateQuantity'])->name('approvals.update-quantity');

        Route::get('/requests/{id}',              [ApprovalController::class, 'show'])->name('requests.show');
        Route::post('/requests/{id}/update-item', [ApprovalController::class, 'updateItem'])->name('requests.update-item');
    });

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
            ->name('dashboard');

        // PIN verify (must be before {id} wildcards)
        Route::post('/departments/verify-pin', [App\Http\Controllers\Admin\DepartmentController::class, 'verifyPin'])
            ->name('departments.verify-pin');
        Route::post('/users/verify-pin', [App\Http\Controllers\Admin\UserManagementController::class, 'verifyPin'])
            ->name('users.verify-pin');

        // User Management
        Route::get('/users',               [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index')
            ->middleware('permission:users');
        Route::post('/users',              [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{id}',          [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
        Route::put('/users/{id}/password', [App\Http\Controllers\Admin\UserManagementController::class, 'changePassword'])->name('users.password');
        Route::delete('/users/{id}',       [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');

        // Supply Management
        Route::get('/supplies',          [SupplyController::class, 'index'])->name('supplies.index')
            ->middleware('permission:supplies');
        Route::get('/supplies/create',   [SupplyController::class, 'create'])->name('supplies.create')
            ->middleware('permission:supplies');
        Route::post('/supplies',         [SupplyController::class, 'store'])->name('supplies.store');
        Route::get('/supplies/{id}/edit',[SupplyController::class, 'edit'])->name('supplies.edit')
            ->middleware('permission:supplies');
        Route::put('/supplies/{id}',     [SupplyController::class, 'update'])->name('supplies.update');
        Route::post('/supplies/{id}/toggle', [SupplyController::class, 'toggleStatus'])->name('supplies.toggle');
        Route::delete('/supplies/{id}',  [SupplyController::class, 'destroy'])->name('supplies.destroy');

        // Low Stock
        Route::get('/low-stock', [App\Http\Controllers\AdminController::class, 'lowStockIndex'])
            ->name('low-stock.index')
            ->middleware('permission:low_stock');

        // Release Management
        Route::get('/releases',                    [ReleaseController::class, 'index'])->name('releases.index')
            ->middleware('permission:releases');
        Route::get('/releases/{id}',               [ReleaseController::class, 'show'])->name('releases.show')
            ->middleware('permission:releases');
        Route::get('/releases/{id}/details',       [ReleaseController::class, 'details'])->name('releases.details');
        Route::post('/releases/{id}/release',      [ReleaseController::class, 'release'])->name('releases.release');
        Route::post('/releases/{id}/requeue',      [ReleaseController::class, 'closeAndRequeue'])->name('admin.releases.requeue');
        Route::post('/releases/{id}/reject',       [ReleaseController::class, 'reject'])->name('releases.reject');
        Route::post('/releases/{id}/approve-return',[ReleaseController::class, 'approveReturn'])->name('releases.approveReturn');
        Route::post('/releases/{id}/reject-return', [ReleaseController::class, 'rejectReturn'])->name('releases.rejectReturn');
        Route::delete('/releases/{id}/delete',     [ReleaseController::class, 'destroy'])->name('releases.destroy');
        Route::delete('/releases/transactions/{id}',[ReleaseController::class, 'destroyTransaction'])->name('admin.releases.transactions.destroy');

        // Voucher
        Route::get('/voucher/{id}', [ReleaseController::class, 'voucher'])->name('voucher');

        // Department Management
        Route::get('/departments',               [App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('departments.index')
            ->middleware('permission:departments');
        Route::post('/departments',              [App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{id}',          [App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{id}',       [App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::put('/departments/{id}/budget',   [App\Http\Controllers\Admin\DepartmentController::class, 'updateBudget'])->name('departments.budget.update');
        Route::post('/departments/reset-budgets',[App\Http\Controllers\Admin\DepartmentController::class, 'resetBudgets'])->name('departments.budget.reset');

        // Audit Logs
        Route::get('/audit-logs',            [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index')
            ->middleware('permission:audit_trail');
        Route::get('/audit-logs/{id}',       [App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('audit-logs.show')
            ->middleware('permission:audit_trail');
        Route::get('/audit-logs/export/csv', [App\Http\Controllers\Admin\AuditLogController::class, 'export'])->name('audit-logs.export');

        // Dashboard department requests (AJAX)
        Route::get('/dashboard/department/{id}/requests', [DashboardController::class, 'departmentRequests'])
            ->name('admin.dashboard.department.requests');
    });
});

require __DIR__.'/auth.php';
