<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Get the friends for this bill.
     */
    public function friends()
    {
        return $this->hasMany(Friend::class);
    }

    /**
     * Get the expenses for this bill.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
