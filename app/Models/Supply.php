<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'description',
        'is_active',
        'stock_quantity',
        'minimum_stock',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'minimum_stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($supply) {
            if (empty($supply->item_code)) {
                $supply->item_code = self::generateItemCode($supply->category);
            }
        });
    }

    public static function generateItemCode($category)
    {
        $prefix = strtoupper(substr($category ?? 'GEN', 0, 3));
        $date = now()->format('ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    public function requestItems()
    {
        return $this->hasMany(RequestItem::class);
    }
}