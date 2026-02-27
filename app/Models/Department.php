<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'cost_center',
        'passcode',
        'annual_budget',
        'allocated_budget',
        'spent_budget',
        'remaining_budget',
        'budget_year',
    ];

    protected $casts = [
        'annual_budget' => 'decimal:2',
        'allocated_budget' => 'decimal:2',
        'spent_budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'budget_year' => 'integer',
    ];

    /**
     * Get users belonging to this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get supply requests from this department
     */
    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class);
    }

    /**
     * Calculate remaining budget
     */
    public function calculateRemainingBudget()
    {
        $this->remaining_budget = $this->allocated_budget - $this->spent_budget;
        return $this->remaining_budget;
    }

    /**
     * Check if department has sufficient budget
     */
    public function hasSufficientBudget($amount)
    {
        return $this->remaining_budget >= $amount;
    }

    /**
     * Deduct amount from budget
     */
    public function deductBudget($amount, $supplyRequestId = null)
    {
        if (!$this->hasSufficientBudget($amount)) {
            throw new \Exception("Insufficient budget. Available: {$this->remaining_budget}, Required: {$amount}");
        }

        $this->spent_budget += $amount;
        $this->calculateRemainingBudget();
        $this->save();

        // Log the transaction
        \Log::info('Budget deducted', [
            'department_id' => $this->id,
            'department' => $this->name,
            'amount' => $amount,
            'supply_request_id' => $supplyRequestId,
            'remaining_budget' => $this->remaining_budget
        ]);

        return true;
    }

    /**
     * Refund amount to budget (if request is cancelled)
     */
    public function refundBudget($amount, $supplyRequestId = null)
    {
        $this->spent_budget = max(0, $this->spent_budget - $amount);
        $this->calculateRemainingBudget();
        $this->save();

        // Log the transaction
        \Log::info('Budget refunded', [
            'department_id' => $this->id,
            'department' => $this->name,
            'amount' => $amount,
            'supply_request_id' => $supplyRequestId,
            'remaining_budget' => $this->remaining_budget
        ]);

        return true;
    }

    /**
     * Get budget utilization percentage
     */
    public function getBudgetUtilizationAttribute()
    {
        if ($this->allocated_budget == 0) {
            return 0;
        }
        return ($this->spent_budget / $this->allocated_budget) * 100;
    }

    /**
     * Get budget status (healthy, warning, critical)
     */
    public function getBudgetStatusAttribute()
    {
        $utilization = $this->budget_utilization;
        
        if ($utilization >= 90) {
            return 'critical';
        } elseif ($utilization >= 75) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }

    /**
     * Get budget status color for UI
     */
    public function getBudgetStatusColorAttribute()
    {
        switch ($this->budget_status) {
            case 'critical':
                return 'red';
            case 'warning':
                return 'yellow';
            case 'healthy':
                return 'green';
            default:
                return 'gray';
        }
    }

    /**
     * Reset budget for new fiscal year
     */
    public function resetBudget($newAnnualBudget = null, $newYear = null)
    {
        $this->budget_year = $newYear ?? date('Y');
        $this->annual_budget = $newAnnualBudget ?? $this->annual_budget;
        $this->allocated_budget = $this->annual_budget;
        $this->spent_budget = 0;
        $this->remaining_budget = $this->allocated_budget;
        $this->save();

        \Log::info('Budget reset for new fiscal year', [
            'department_id' => $this->id,
            'department' => $this->name,
            'year' => $this->budget_year,
            'budget' => $this->annual_budget
        ]);

        return true;
    }

    /**
     * Get total approved requests cost for current year
     */
    public function getApprovedRequestsCostAttribute()
    {
        return $this->supplyRequests()
            ->whereNotNull('manager_approved_at')
            ->whereYear('manager_approved_at', $this->budget_year)
            ->sum('actual_cost') ?? 0;
    }

    /**
     * Get pending requests estimated cost
     */
    public function getPendingRequestsCostAttribute()
    {
        return $this->supplyRequests()
            ->where('status', 'pending_approval')
            ->sum('estimated_cost') ?? 0;
    }
}