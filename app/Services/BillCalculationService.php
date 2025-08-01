<?php

namespace App\Services;

use App\Models\Bill;
use Illuminate\Support\Collection;

/**
 * Service class for handling complex bill splitting calculations.
 * 
 * This service provides methods to calculate individual spending, shares, net balances,
 * and optimal settlement plans for splitting bills among friends. It uses precise
 * decimal calculations to handle money properly and implements a greedy algorithm
 * for optimal settlement recommendations.
 * 
 * @package App\Services
 */
class BillCalculationService
{
    /**
     * Calculate how much each person paid for expenses.
     * 
     * This method aggregates all expenses paid by each friend in the bill
     * and returns a collection with friend objects and their total spending.
     * 
     * @param Bill $bill The bill to calculate spending for
     * @return Collection Collection of arrays with 'friend' and 'amount' keys
     */
    public function calculateIndividualSpending(Bill $bill): Collection
    {
        $spending = collect();
        
        foreach ($bill->friends as $friend) {
            $totalPaid = $friend->paidExpenses()
                ->where('bill_id', $bill->id)
                ->sum('amount');
            
            $spending->put($friend->id, [
                'friend' => $friend,
                'amount' => round($totalPaid, 2)
            ]);
        }
        
        return $spending;
    }

    /**
     * Calculate how much each person should pay based on shared expenses.
     * 
     * This method distributes the cost of each expense equally among the friends
     * who shared that expense. It handles cases where expenses have different
     * sharing patterns and calculates the fair share for each person.
     * 
     * @param Bill $bill The bill to calculate shares for
     * @return Collection Collection of arrays with 'friend' and 'amount' keys
     */
    public function calculateIndividualShares(Bill $bill): Collection
    {
        $shares = collect();
        
        // Initialize shares for all friends
        foreach ($bill->friends as $friend) {
            $shares->put($friend->id, [
                'friend' => $friend,
                'amount' => 0.0
            ]);
        }
        
        // Calculate shares for each expense
        foreach ($bill->expenses as $expense) {
            if ($expense->amount <= 0) {
                continue; // Skip zero amounts
            }
            
            $sharingFriends = $expense->sharedBy;
            
            if ($sharingFriends->isEmpty()) {
                continue; // Skip expenses with no sharing
            }
            
            $sharePerPerson = round($expense->amount / $sharingFriends->count(), 2);
            
            foreach ($sharingFriends as $sharingFriend) {
                $currentShare = $shares->get($sharingFriend->id)['amount'];
                $shares->put($sharingFriend->id, [
                    'friend' => $sharingFriend,
                    'amount' => round($currentShare + $sharePerPerson, 2)
                ]);
            }
        }
        
        return $shares;
    }

    /**
     * Calculate net balances (who owes what).
     * 
     * This method determines who should receive money (positive balance) and who
     * should pay money (negative balance) by comparing what each person paid
     * versus what they should pay based on their share of expenses.
     * 
     * @param Bill $bill The bill to calculate net balances for
     * @return Collection Collection with friend, paid, should_pay, net_balance, and status
     */
    public function calculateNetBalances(Bill $bill): Collection
    {
        $spending = $this->calculateIndividualSpending($bill);
        $shares = $this->calculateIndividualShares($bill);
        $balances = collect();
        
        foreach ($bill->friends as $friend) {
            $paid = $spending->get($friend->id)['amount'] ?? 0.0;
            $shouldPay = $shares->get($friend->id)['amount'] ?? 0.0;
            $netBalance = round($paid - $shouldPay, 2);
            
            $balances->put($friend->id, [
                'friend' => $friend,
                'paid' => $paid,
                'should_pay' => $shouldPay,
                'net_balance' => $netBalance,
                'status' => $this->getBalanceStatus($netBalance)
            ]);
        }
        
        return $balances;
    }

    /**
     * Generate optimal settlement plan using greedy algorithm.
     * 
     * This method implements a greedy algorithm to minimize the number of transactions
     * needed to settle all debts. It works by:
     * 1. Separating creditors (people who should receive money) from debtors (people who owe money)
     * 2. Sorting them by balance amount (largest first)
     * 3. Matching the largest creditors with the largest debtors
     * 4. Creating transactions that settle as much debt as possible in each step
     * 
     * The algorithm ensures minimal transactions while maintaining mathematical accuracy
     * using precise decimal calculations to avoid floating-point errors.
     * 
     * @param Bill $bill The bill to generate settlement plan for
     * @return array Array containing 'transactions' and 'message' keys
     */
    public function generateSettlementPlan(Bill $bill): array
    {
        $balances = $this->calculateNetBalances($bill);
        
        // Handle edge cases
        if ($balances->count() <= 1) {
            return [
                'transactions' => [],
                'message' => 'No settlement needed - single person bill'
            ];
        }
        
        // Separate creditors (positive balance) and debtors (negative balance)
        $creditors = $balances->filter(function ($balance) {
            return $balance['net_balance'] > 0;
        })->sortByDesc('net_balance');
        
        $debtors = $balances->filter(function ($balance) {
            return $balance['net_balance'] < 0;
        })->sortBy('net_balance');
        
        if ($creditors->isEmpty() || $debtors->isEmpty()) {
            return [
                'transactions' => [],
                'message' => 'No settlement needed - all balances are zero'
            ];
        }
        
        $transactions = [];
        $creditorBalances = $creditors->toArray();
        $debtorBalances = $debtors->toArray();
        
        // Greedy algorithm: match largest creditors with largest debtors
        // This minimizes the total number of transactions needed
        foreach ($creditorBalances as $creditorId => $creditor) {
            // Skip creditors with negligible balances (handles floating-point precision)
            if (abs($creditor['net_balance']) < 0.01) {
                continue;
            }
            
            $remainingCredit = abs($creditor['net_balance']);
            
            // Try to settle this creditor's balance with available debtors
            foreach ($debtorBalances as $debtorId => $debtor) {
                // Skip debtors with negligible balances
                if (abs($debtor['net_balance']) < 0.01) {
                    continue;
                }
                
                $remainingDebt = abs($debtor['net_balance']);
                // Transfer the smaller of the two amounts (either full debt or full credit)
                $transferAmount = min($remainingCredit, $remainingDebt);
                
                // Only create transaction if amount is financially significant
                if ($transferAmount > 0.01) {
                    $transactions[] = [
                        'from' => $debtor['friend'],
                        'to' => $creditor['friend'],
                        'amount' => round($transferAmount, 2)
                    ];
                    
                    // Update remaining amounts for this iteration
                    $remainingCredit -= $transferAmount;
                    $debtorBalances[$debtorId]['net_balance'] += $transferAmount; // Reduce debt (negative becomes less negative)
                    
                    // If creditor is fully settled, move to next creditor
                    if ($remainingCredit < 0.01) {
                        break;
                    }
                }
            }
        }
        
        return [
            'transactions' => $transactions,
            'message' => count($transactions) > 0 ? null : 'No transactions needed'
        ];
    }

    /**
     * Get a comprehensive summary of all calculations for a bill.
     * 
     * This is the main public method that consolidates all calculations
     * and provides a complete picture of the bill's financial state.
     * 
     * @param Bill $bill The bill to generate summary for
     * @return array Complete summary with spending, shares, balances, settlement, and stats
     */
    public function getBillSummary(Bill $bill): array
    {
        $spending = $this->calculateIndividualSpending($bill);
        $shares = $this->calculateIndividualShares($bill);
        $balances = $this->calculateNetBalances($bill);
        $settlement = $this->generateSettlementPlan($bill);
        
        return [
            'bill' => $bill,
            'spending' => $spending,
            'shares' => $shares,
            'balances' => $balances,
            'settlement' => $settlement,
            'total_spent' => round($spending->sum('amount'), 2),
            'total_shared' => round($shares->sum('amount'), 2),
            'summary_stats' => $this->getSummaryStats($balances)
        ];
    }

    /**
     * Get balance status for display purposes.
     * 
     * Categorizes a person's balance into human-readable status:
     * - settled: Balance is effectively zero (within 1 cent)
     * - creditor: Should receive money (positive balance)
     * - debtor: Should pay money (negative balance)
     * 
     * @param float $balance The net balance to categorize
     * @return string The status category
     */
    private function getBalanceStatus(float $balance): string
    {
        if (abs($balance) < 0.01) {
            return 'settled';
        } elseif ($balance > 0) {
            return 'creditor';
        } else {
            return 'debtor';
        }
    }

    /**
     * Get summary statistics for the bill.
     * 
     * Provides aggregate statistics about the bill's financial state,
     * including counts of different balance types and total amounts.
     * Used for dashboard displays and summary views.
     * 
     * @param Collection $balances Collection of calculated balances
     * @return array Array of statistical summaries
     */
    private function getSummaryStats(Collection $balances): array
    {
        $totalCreditors = $balances->where('status', 'creditor')->count();
        $totalDebtors = $balances->where('status', 'debtor')->count();
        $totalSettled = $balances->where('status', 'settled')->count();
        
        $totalOwed = $balances->where('status', 'debtor')->sum('net_balance');
        $totalToReceive = $balances->where('status', 'creditor')->sum('net_balance');
        
        return [
            'creditors_count' => $totalCreditors,
            'debtors_count' => $totalDebtors,
            'settled_count' => $totalSettled,
            'total_owed' => round(abs($totalOwed), 2),
            'total_to_receive' => round($totalToReceive, 2),
            'is_balanced' => abs($totalOwed + $totalToReceive) < 0.01
        ];
    }
} 