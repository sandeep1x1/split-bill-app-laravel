<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Expense;
use Illuminate\Http\Request;

/**
 * Service class for handling expense business logic.
 * 
 * This service encapsulates the business logic for creating, updating,
 * and deleting expenses, keeping controllers thin and focused on
 * handling HTTP concerns.
 * 
 * @package App\Services
 */
class ExpenseService
{
    /**
     * Create a new expense for a bill.
     * 
     * Handles the business logic of creating an expense including
     * data preparation, expense creation, and relationship management.
     * 
     * @param Bill $bill The bill to add the expense to
     * @param array $data Validated expense data
     * @return Expense The created expense
     */
    public function createExpense(Bill $bill, array $data): Expense
    {
        // Prevent adding expenses to settled bills
        if ($bill->status === 'settled') {
            throw new \InvalidArgumentException('Cannot add expenses to a settled bill.');
        }

        // Create the expense with proper data formatting
        $expense = Expense::create([
            'bill_id' => $bill->id,
            'title' => trim($data['title']),
            'amount' => round($data['amount'], 2), // Ensure proper decimal precision
            'paid_by' => $data['paid_by']
        ]);

        // Attach friends who share this expense
        $expense->sharedBy()->attach($data['shared_by']);

        return $expense;
    }

    /**
     * Update an existing expense.
     * 
     * Handles the business logic of updating an expense including
     * data validation, expense updates, and relationship synchronization.
     * 
     * @param Bill $bill The bill containing the expense
     * @param Expense $expense The expense to update
     * @param array $data Validated expense data
     * @return Expense The updated expense
     */
    public function updateExpense(Bill $bill, Expense $expense, array $data): Expense
    {
        // Prevent updating expenses on settled bills
        if ($bill->status === 'settled') {
            throw new \InvalidArgumentException('Cannot update expenses on a settled bill.');
        }

        // Update the expense with proper data formatting
        $expense->update([
            'title' => trim($data['title']),
            'amount' => round($data['amount'], 2), // Ensure proper decimal precision
            'paid_by' => $data['paid_by']
        ]);

        // Synchronize friends who share this expense
        $expense->sharedBy()->sync($data['shared_by']);

        return $expense;
    }

    /**
     * Delete an expense.
     * 
     * Handles the business logic of deleting an expense including
     * cleanup of relationships and proper data integrity.
     * 
     * @param Bill $bill The bill containing the expense
     * @param Expense $expense The expense to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteExpense(Bill $bill, Expense $expense): bool
    {
        // Prevent deleting expenses from settled bills
        if ($bill->status === 'settled') {
            throw new \InvalidArgumentException('Cannot delete expenses from a settled bill.');
        }

        // Clean up relationships first
        $expense->sharedBy()->detach();
        
        // Delete the expense
        return $expense->delete();
    }

    /**
     * Check if an expense can be modified.
     * 
     * Validates whether an expense can be created, updated, or deleted
     * based on the current state of the bill.
     * 
     * @param Bill $bill The bill to check
     * @return bool Whether expenses can be modified
     */
    public function canModifyExpenses(Bill $bill): bool
    {
        return $bill->status === 'active';
    }
}