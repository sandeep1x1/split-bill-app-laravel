<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bill_id',
    ];

    /**
     * Get the bill that owns this friend.
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get the expenses that this friend shares.
     */
    public function sharedExpenses()
    {
        return $this->belongsToMany(Expense::class, 'expense_shares');
    }

    /**
     * Get the expenses that this friend paid for.
     */
    public function paidExpenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }
}
