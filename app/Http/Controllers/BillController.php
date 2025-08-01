<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Services\BillCalculationService;

/**
 * Controller for managing bills and bill operations.
 * 
 * This controller handles HTTP requests for bill management including
 * creating, viewing, and settling bills. It uses the BillCalculationService
 * for complex calculations and maintains thin controller methods.
 */
class BillController extends Controller
{
    /**
     * Display a listing of all bills (dashboard).
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $bills = Bill::with('friends')->latest()->get();
        return view('bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new bill.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created bill in storage.
     * 
     * @param Request $request The HTTP request containing bill data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Enhanced validation with custom messages
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/'
            ],
            'friends' => [
                'required',
                'array',
                'min:1',
                'max:20'
            ],
            'friends.*' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z\s]+$/'
            ]
        ], [
            'name.required' => 'Bill name is required.',
            'name.min' => 'Bill name must be at least 3 characters.',
            'name.max' => 'Bill name cannot exceed 255 characters.',
            'name.regex' => 'Bill name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'friends.required' => 'At least one friend is required.',
            'friends.min' => 'At least one friend is required.',
            'friends.max' => 'Maximum 20 friends allowed per bill.',
            'friends.*.required' => 'Friend name is required.',
            'friends.*.min' => 'Friend name must be at least 2 characters.',
            'friends.*.max' => 'Friend name cannot exceed 50 characters.',
            'friends.*.regex' => 'Friend name can only contain letters and spaces.'
        ]);

        // Check for duplicate friend names
        $friendNames = array_map('trim', $request->friends);
        $uniqueNames = array_unique($friendNames);
        
        if (count($friendNames) !== count($uniqueNames)) {
            return back()
                ->withInput()
                ->withErrors(['friends' => 'Duplicate friend names are not allowed.']);
        }

        // Create the bill
        $bill = Bill::create([
            'name' => trim($request->name)
        ]);

        // Create friends for this bill
        foreach ($uniqueNames as $friendName) {
            if (!empty($friendName)) {
                Friend::create([
                    'name' => $friendName,
                    'bill_id' => $bill->id
                ]);
            }
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill created successfully!');
    }

    /**
     * Display the specified bill with its expenses and calculations.
     * 
     * @param Bill $bill The bill to display
     * @param BillCalculationService $calc Service for calculating bill summaries
     * @return \Illuminate\View\View
     */
    public function show(Bill $bill, BillCalculationService $calc)
    {
        $bill->load(['friends', 'expenses.paidBy', 'expenses.sharedBy']);
        $summary = $calc->getBillSummary($bill);
        return view('bills.show', compact('bill', 'summary'));
    }

    /**
     * Settle the bill - mark it as settled.
     * 
     * Changes the bill status to 'settled' to prevent further modifications.
     * 
     * @param Bill $bill The bill to settle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settle(Bill $bill)
    {
        // Check if the bill is already settled
        if ($bill->status === 'settled') {
            return back()->with('warning', 'This bill is already settled.');
        }

        // Update the bill status to settled
        $bill->update(['status' => 'settled']);

        return back()->with('success', 'Bill has been settled successfully!');
    }
}
