<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'title',
        'amount',
        'paid_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the bill that owns this expense.
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get the friend who paid for this expense.
     */
    public function paidBy()
    {
        return $this->belongsTo(Friend::class, 'paid_by');
    }

    /**
     * Get the friends who share this expense.
     */
    public function sharedBy()
    {
        return $this->belongsToMany(Friend::class, 'expense_shares');
    }
}
