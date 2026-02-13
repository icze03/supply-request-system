<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supply_request_id',
        'supply_id',
        'item_name',
        'item_code',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the supply request that owns the item.
     */
    public function supplyRequest()
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    /**
     * Get the supply for the item.
     */
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}