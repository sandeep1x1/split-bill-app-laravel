<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Friend;
use App\Models\Expense;
use Illuminate\Support\Collection;

class BillCalculationService
{
    /**
     * Calculate how much each person paid for expenses
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
     * Calculate how much each person should pay based on shared expenses
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
     * Calculate net balances (who owes what)
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
     * Generate optimal settlement plan using greedy algorithm
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
        foreach ($creditorBalances as $creditorId => $creditor) {
            if (abs($creditor['net_balance']) < 0.01) {
                continue; // Skip if balance is effectively zero
            }
            
            $remainingCredit = abs($creditor['net_balance']);
            
            foreach ($debtorBalances as $debtorId => $debtor) {
                if (abs($debtor['net_balance']) < 0.01) {
                    continue; // Skip if balance is effectively zero
                }
                
                $remainingDebt = abs($debtor['net_balance']);
                $transferAmount = min($remainingCredit, $remainingDebt);
                
                if ($transferAmount > 0.01) { // Only create transaction if amount is significant
                    $transactions[] = [
                        'from' => $debtor['friend'],
                        'to' => $creditor['friend'],
                        'amount' => round($transferAmount, 2)
                    ];
                    
                    // Update remaining amounts
                    $remainingCredit -= $transferAmount;
                    $debtorBalances[$debtorId]['net_balance'] += $transferAmount;
                    
                    if ($remainingCredit < 0.01) {
                        break; // Creditor is fully settled
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
     * Get a summary of all calculations for a bill
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
     * Get balance status for display
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
     * Get summary statistics for the bill
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