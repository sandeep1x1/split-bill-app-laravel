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
        // Enhanced validation with custom messages
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'paid_by' => [
                'required',
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('The selected person must be a friend in this bill.');
                    }
                }
            ],
            'shared_by' => [
                'required',
                'array',
                'min:1',
                'max:20'
            ],
            'shared_by.*' => [
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('All selected friends must be part of this bill.');
                    }
                }
            ]
        ], [
            'title.required' => 'Expense title is required.',
            'title.min' => 'Expense title must be at least 2 characters.',
            'title.max' => 'Expense title cannot exceed 255 characters.',
            'title.regex' => 'Expense title can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least $0.01.',
            'amount.max' => 'Amount cannot exceed $999,999.99.',
            'amount.regex' => 'Amount must have maximum 2 decimal places.',
            'paid_by.required' => 'Please select who paid for this expense.',
            'paid_by.exists' => 'The selected person is not valid.',
            'shared_by.required' => 'Please select who shares this expense.',
            'shared_by.min' => 'At least one person must share this expense.',
            'shared_by.max' => 'Maximum 20 people can share an expense.',
            'shared_by.*.exists' => 'One or more selected friends are not valid.'
        ]);

        // Additional validation: paid_by must be in shared_by
        if (!in_array($request->paid_by, $request->shared_by)) {
            return back()
                ->withInput()
                ->withErrors(['paid_by' => 'The person who paid must also share this expense.']);
        }

        // Create the expense
        $expense = Expense::create([
            'bill_id' => $bill->id,
            'title' => trim($request->title),
            'amount' => round($request->amount, 2),
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
        // Enhanced validation with custom messages (same as store)
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/'
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'paid_by' => [
                'required',
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('The selected person must be a friend in this bill.');
                    }
                }
            ],
            'shared_by' => [
                'required',
                'array',
                'min:1',
                'max:20'
            ],
            'shared_by.*' => [
                'exists:friends,id',
                function ($attribute, $value, $fail) use ($bill) {
                    if (!$bill->friends->contains('id', $value)) {
                        $fail('All selected friends must be part of this bill.');
                    }
                }
            ]
        ], [
            'title.required' => 'Expense title is required.',
            'title.min' => 'Expense title must be at least 2 characters.',
            'title.max' => 'Expense title cannot exceed 255 characters.',
            'title.regex' => 'Expense title can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least $0.01.',
            'amount.max' => 'Amount cannot exceed $999,999.99.',
            'amount.regex' => 'Amount must have maximum 2 decimal places.',
            'paid_by.required' => 'Please select who paid for this expense.',
            'paid_by.exists' => 'The selected person is not valid.',
            'shared_by.required' => 'Please select who shares this expense.',
            'shared_by.min' => 'At least one person must share this expense.',
            'shared_by.max' => 'Maximum 20 people can share an expense.',
            'shared_by.*.exists' => 'One or more selected friends are not valid.'
        ]);

        // Additional validation: paid_by must be in shared_by
        if (!in_array($request->paid_by, $request->shared_by)) {
            return back()
                ->withInput()
                ->withErrors(['paid_by' => 'The person who paid must also share this expense.']);
        }

        // Update the expense
        $expense->update([
            'title' => trim($request->title),
            'amount' => round($request->amount, 2),
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
