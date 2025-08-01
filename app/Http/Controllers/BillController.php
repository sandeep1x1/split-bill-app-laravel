<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Friend;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Display a listing of all bills (dashboard).
     */
    public function index()
    {
        $bills = Bill::with('friends')->latest()->get();
        return view('bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new bill.
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created bill in storage.
     */
    public function store(Request $request)
    {
        // Basic validation - will be enhanced in Phase 3
        $request->validate([
            'name' => 'required|string|max:255',
            'friends' => 'required|array|min:1',
            'friends.*' => 'required|string|max:255'
        ]);

        // Create the bill
        $bill = Bill::create([
            'name' => $request->name
        ]);

        // Create friends for this bill
        foreach ($request->friends as $friendName) {
            if (!empty(trim($friendName))) {
                Friend::create([
                    'name' => trim($friendName),
                    'bill_id' => $bill->id
                ]);
            }
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill created successfully!');
    }

    /**
     * Display the specified bill with its expenses.
     */
    public function show(Bill $bill)
    {
        $bill->load(['friends', 'expenses.paidBy', 'expenses.sharedBy']);
        return view('bills.show', compact('bill'));
    }
}
