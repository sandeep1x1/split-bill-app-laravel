<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request, Bill $bill)
    {
        // Basic validation - will be enhanced in Phase 3
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'paid_by' => 'required|exists:friends,id',
            'shared_by' => 'required|array|min:1',
            'shared_by.*' => 'exists:friends,id'
        ]);

        // Create the expense
        $expense = Expense::create([
            'bill_id' => $bill->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'paid_by' => $request->paid_by
        ]);

        // Attach friends who share this expense
        $expense->sharedBy()->attach($request->shared_by);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Expense added successfully!');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Bill $bill, Expense $expense)
    {
        // Basic validation - will be enhanced in Phase 3
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'paid_by' => 'required|exists:friends,id',
            'shared_by' => 'required|array|min:1',
            'shared_by.*' => 'exists:friends,id'
        ]);

        // Update the expense
        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'paid_by' => $request->paid_by
        ]);

        // Sync friends who share this expense
        $expense->sharedBy()->sync($request->shared_by);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Bill $bill, Expense $expense)
    {
        // Detach all friends from this expense
        $expense->sharedBy()->detach();
        
        // Delete the expense
        $expense->delete();

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Expense deleted successfully!');
    }
}
