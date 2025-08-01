<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Bill;
use App\Models\Expense;
use App\Services\ExpenseService;

/**
 * Controller for managing expenses within bills.
 * 
 * This controller handles HTTP requests for expense operations,
 * delegating business logic to the ExpenseService and using
 * form requests for validation.
 */
class ExpenseController extends Controller
{
    /**
     * Store a newly created expense in storage.
     * 
     * @param StoreExpenseRequest $request Validated request data
     * @param Bill $bill The bill to add the expense to
     * @param ExpenseService $expenseService Service handling business logic
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreExpenseRequest $request, Bill $bill, ExpenseService $expenseService)
    {
        try {
            $expense = $expenseService->createExpense($bill, $request->validated());
            
            return redirect()->route('bills.show', $bill)
                ->with('success', 'Expense added successfully!');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified expense in storage.
     * 
     * @param StoreExpenseRequest $request Validated request data
     * @param Bill $bill The bill containing the expense
     * @param Expense $expense The expense to update
     * @param ExpenseService $expenseService Service handling business logic
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreExpenseRequest $request, Bill $bill, Expense $expense, ExpenseService $expenseService)
    {
        try {
            $expenseService->updateExpense($bill, $expense, $request->validated());
            
            return redirect()->route('bills.show', $bill)
                ->with('success', 'Expense updated successfully!');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified expense from storage.
     * 
     * @param Bill $bill The bill containing the expense
     * @param Expense $expense The expense to delete
     * @param ExpenseService $expenseService Service handling business logic
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Bill $bill, Expense $expense, ExpenseService $expenseService)
    {
        try {
            $expenseService->deleteExpense($bill, $expense);
            
            return redirect()->route('bills.show', $bill)
                ->with('success', 'Expense deleted successfully!');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
