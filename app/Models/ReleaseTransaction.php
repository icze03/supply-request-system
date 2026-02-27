<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReleaseTransaction extends Model
{
    protected $fillable = [
        'supply_request_id',
        'round',
        'serial_number',
        'ro_number',
        'notes',
        'released_by',
        'is_final_release',
        'items_snapshot',
        'total_items_in_request',
        'items_fully_released_this_round',
        'items_still_pending_after',
    ];

    protected $casts = [
        'items_snapshot'   => 'array',
        'is_final_release' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function supplyRequest()
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Total quantity released across all items in this transaction round.
     */
    public function totalQtyReleased(): int
    {
        return collect($this->items_snapshot)->sum('qty_released');
    }

    /**
     * Total quantity still pending after this transaction round.
     */
    public function totalQtyPending(): int
    {
        return collect($this->items_snapshot)->sum('qty_remaining_after');
    }
}