<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'sr_number',
        'request_type',
        'budget_type',
        'purpose',
        'special_item_description',
        'status',
        'manager_approved_by',
        'manager_approved_at',
        'manager_rejected_at',
        'manager_notes',
        'admin_released_by',
        'admin_released_at',
        'admin_rejected_by',
        'admin_rejected_at',
        'admin_notes',
        'serial_number',
        'ro_number',
    ];

    protected $casts = [
        'manager_approved_at' => 'datetime',
        'manager_rejected_at' => 'datetime',
        'admin_released_at' => 'datetime',
        'admin_rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate SR number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($request) {
            if (empty($request->sr_number)) {
                $request->sr_number = self::generateSRNumber($request->department_id);
            }
        });
    }

    /**
     * Generate SR number
     * Format: SR-DEPTCODE-YYYYMMDD-0001
     */
    public static function generateSRNumber($departmentId)
    {
        $department = Department::find($departmentId);
        $deptCode = $department ? $department->code : 'GEN';
        $date = now()->format('Ymd');
        
        // Get count of requests for this department today
        $count = self::where('department_id', $departmentId)
            ->whereDate('created_at', now())
            ->count() + 1;
        
        return sprintf('SR-%s-%s-%04d', $deptCode, $date, $count);
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Manager',
            'manager_approved' => 'Pending Release',
            'manager_rejected' => 'Manager Rejected',
            'admin_released' => 'Released',
            'admin_rejected' => 'Admin Rejected',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Get CSS class for status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'manager_approved' => 'bg-blue-100 text-blue-800',
            'manager_rejected' => 'bg-red-100 text-red-800',
            'admin_released' => 'bg-green-100 text-green-800',
            'admin_rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if request is in pending status
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is manager approved
     */
    public function isManagerApproved(): bool
    {
        return $this->status === 'manager_approved';
    }

    /**
     * Check if request is released
     */
    public function isReleased(): bool
    {
        return $this->status === 'admin_released';
    }

    /**
     * Scope to filter requests by department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Relationship: User who created the request
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relationship: Request items
     */
    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    /**
     * Relationship: Manager who approved
     */
    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    /**
     * Relationship: Admin who released
     */
    public function adminReleaser()
    {
        return $this->belongsTo(User::class, 'admin_released_by');
    }

    /**
     * Relationship: Admin who rejected
     */
    public function adminRejecter()
    {
        return $this->belongsTo(User::class, 'admin_rejected_by');
    }

    /**
 * Check if request is partially allocated
 */
public function isPartiallyAllocated()
{
    if ($this->request_type !== 'standard') {
        return false;
    }
    
    foreach ($this->items as $item) {
        if ($item->allocated_quantity > 0 && $item->allocated_quantity < $item->quantity) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if request is fully allocated
 */
public function isFullyAllocated()
{
    if ($this->request_type !== 'standard') {
        return true;
    }
    
    foreach ($this->items as $item) {
        if (!$item->is_fully_allocated) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get allocation status badge
 */
public function getAllocationBadge()
{
    if ($this->isPartiallyAllocated()) {
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"> Partial</span>';
    }
    
    if ($this->isFullyAllocated()) {
        return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">✓ Full</span>';
    }
    
    return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>';
}

public function hasActiveReturnRequest(): bool
{
    return in_array($this->status, ['return_requested']);
}

public function releaseTransactions()
{
    return $this->hasMany(\App\Models\ReleaseTransaction::class, 'supply_request_id')
                ->orderBy('round');
}

/**
 * Track parent SR when this was auto-created from a re-queue.
 * Requires parent_sr_id column in supply_requests table.
 */
public function parentRequest()
{
    return $this->belongsTo(SupplyRequest::class, 'parent_sr_id');
}

public function childRequests()
{
    return $this->hasMany(SupplyRequest::class, 'parent_sr_id');
}
}