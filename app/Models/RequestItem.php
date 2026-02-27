<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $fillable = [
        'supply_request_id',
        'supply_id',
        'item_name',
        'item_code',
        'quantity',
        'original_quantity',
        'released_quantity',
        'remaining_quantity',
        'allocated_quantity',
        'is_fully_allocated',
    ];

    protected $casts = [
        'quantity'           => 'integer',
        'original_quantity'  => 'integer',
        'released_quantity'  => 'integer',
        'remaining_quantity' => 'integer',
        'allocated_quantity' => 'integer',
        'is_fully_allocated' => 'boolean',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function supplyRequest()
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    // =============================================
    // ACCESSORS (computed, don't override DB columns)
    // =============================================

    public function getAllocationPercentageAttribute()
    {
        $original = $this->original_quantity ?? $this->quantity;
        if ($original == 0) return 0;
        return round(($this->released_quantity / $original) * 100, 1);
    }

    public function getAllocationStatusAttribute()
    {
        $remaining = $this->remaining_quantity ?? ($this->quantity - ($this->released_quantity ?? 0));
        if ($remaining <= 0) return 'full';
        if (($this->released_quantity ?? 0) > 0) return 'partial';
        return 'none';
    }
}