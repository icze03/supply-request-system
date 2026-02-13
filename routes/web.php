<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\CatalogController;
use App\Http\Controllers\Employee\RequestController;
use App\Http\Controllers\Manager\ApprovalController;
use App\Http\Controllers\Admin\SupplyController;
use App\Http\Controllers\Admin\ReleaseController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    
    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard (role-based redirect)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =============================================
    // EMPLOYEE ROUTES
    // =============================================
    Route::middleware('employee')->prefix('employee')->name('employee.')->group(function () {
        
        // Catalog
        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
        Route::get('/catalog/supplies', [CatalogController::class, 'getSupplies'])->name('catalog.supplies');
        Route::get('/catalog/{id}', [CatalogController::class, 'show'])->name('catalog.show');

        // Requests
        Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{id}', [RequestController::class, 'show'])->name('requests.show');
        Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
        Route::post('/requests/special', [RequestController::class, 'storeSpecial'])->name('requests.special');
        Route::delete('/requests/{id}', [RequestController::class, 'cancel'])->name('requests.cancel');
    });

    // =============================================
    // MANAGER ROUTES
    // =============================================
    Route::middleware('manager')->prefix('manager')->name('manager.')->group(function () {
        
        // Passcode verification
        Route::get('/verify', [ApprovalController::class, 'verifyPasscode'])->name('verify');
        Route::post('/verify', [ApprovalController::class, 'checkPasscode'])->name('verify.check');
        Route::get('', [ApprovalController::class,  'index'])->name('dashboard');

        // Approvals - FIXED: Changed 'approvals' to 'approvals.index'
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('/approvals/{id}/update-quantity', [ApprovalController::class, 'updateQuantity'])->name('approvals.update-quantity');
        
        // Request detail view
        Route::get('/requests/{id}', [ApprovalController::class, 'show'])->name('requests.show');
        Route::post('/requests/{id}/update-item', [ApprovalController::class, 'updateItem'])->name('requests.update-item');
    });

    // =============================================
    // ADMIN ROUTES
    // =============================================

    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
        ->name('dashboard');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])
        ->name('users.index');
    Route::post('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])
        ->name('users.store');
    Route::put('/users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])
        ->name('users.update');
    Route::put('/users/{id}/password', [App\Http\Controllers\Admin\UserManagementController::class, 'changePassword'])
        ->name('users.password');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])
        ->name('users.destroy');

    // Supply Management - ALL ROUTES DEFINED
    Route::get('/supplies', [App\Http\Controllers\AdminController::class, 'suppliesIndex'])
        ->name('supplies.index');
    Route::get('/supplies/create', [App\Http\Controllers\AdminController::class, 'suppliesCreate'])
        ->name('supplies.create');
    Route::post('/supplies', [App\Http\Controllers\AdminController::class, 'suppliesStore'])
        ->name('supplies.store');  // THIS WAS MISSING!
    Route::get('/supplies/{id}/edit', [App\Http\Controllers\AdminController::class, 'suppliesEdit'])
        ->name('supplies.edit');
    Route::put('/supplies/{id}', [App\Http\Controllers\AdminController::class, 'suppliesUpdate'])
        ->name('supplies.update');
    Route::post('/supplies/{id}/toggle', [App\Http\Controllers\AdminController::class, 'suppliesToggle'])
        ->name('supplies.toggle');
    Route::delete('/supplies/{id}', [App\Http\Controllers\AdminController::class, 'suppliesDestroy'])
        ->name('supplies.destroy');

    // Low Stock (Separate Page)
    Route::get('/low-stock', [App\Http\Controllers\AdminController::class, 'lowStockIndex'])
        ->name('low-stock.index');

    // Release Management
    Route::get('/releases', [App\Http\Controllers\Admin\ReleaseController::class, 'index'])
        ->name('releases.index');
    Route::get('/releases/{id}', [App\Http\Controllers\Admin\ReleaseController::class, 'show'])
        ->name('releases.show');
    Route::post('/releases/{id}/release', [App\Http\Controllers\Admin\ReleaseController::class, 'release'])
        ->name('releases.release');
    Route::post('/releases/{id}/reject', [App\Http\Controllers\Admin\ReleaseController::class, 'reject'])
        ->name('releases.reject');
    Route::delete('/releases/{id}/delete', [App\Http\Controllers\Admin\ReleaseController::class, 'destroy'])
        ->name('releases.destroy');    
    
    // Voucher
    Route::get('/voucher/{id}', [App\Http\Controllers\Admin\ReleaseController::class, 'voucher'])
        ->name('voucher');
});
});

require __DIR__.'/auth.php';