@extends('layouts.app')

@section('title', $bill->name)

@section('content')
<x-breadcrumbs>
    <li>
        <a href="{{ route('home') }}" class="inline-flex items-center hover:underline">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18"></path>
            </svg>
            Dashboard
        </a>
    </li>
    <li>
        <span class="inline-flex items-center text-blue-600 font-semibold">
            <svg class="w-4 h-4 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            Bill Details
        </span>
    </li>
</x-breadcrumbs>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" aria-label="Back to dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $bill->name }}</h1>
                <p class="text-sm text-gray-500">Created {{ $bill->created_at->diffForHumans() }}</p>
            </div>
        </div>
        
        @if($bill->status === 'active')
            <div x-data="{ showSettleConfirm: false }">
                <button @click="showSettleConfirm = true" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Settle Bill
                </button>

                <!-- Settlement Confirmation Modal -->
                <div x-show="showSettleConfirm" 
                     x-cloak 
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
                     @click.away="showSettleConfirm = false"
                     @keydown.escape.window="showSettleConfirm = false">
                    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900">Settle Bill?</h2>
                        <p class="text-gray-600 mb-4">
                            Are you sure you want to settle <span class="font-bold">{{ $bill->name }}</span>? 
                            This will mark the bill as settled and prevent further expenses from being added.
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    @click="showSettleConfirm = false" 
                                    class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Cancel
                            </button>
                            <form action="{{ route('bills.settle', $bill) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Yes, Settle Bill
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 rounded-md">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-800 font-medium">Bill Settled</span>
            </div>
        @endif
    </div>
    @if($bill->status === 'active')
        <div class="text-xs text-gray-400 mb-2">Tip: You can add, edit, or delete expenses below. Use the settlement summary to see who owes what. Click "Settle Bill" when everyone has paid their share.</div>
    @else
        <div class="text-xs text-green-600 mb-2">✓ This bill has been settled. No further changes can be made to expenses.</div>
    @endif

    <!-- Settlement Summary -->
    <div class="bg-white rounded-lg shadow-sm border border-blue-200">
        <div class="px-6 py-4 border-b border-blue-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a5 5 0 00-10 0v2a5 5 0 00-2 4v5a2 2 0 002 2h10a2 2 0 002-2v-5a5 5 0 00-2-4z" />
                </svg>
                Settlement Summary
            </h3>
            <span class="text-xs text-gray-500">All values in $</span>
        </div>
        <div class="p-6 space-y-6">
            <!-- Individual Balances -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-2">Individual Balances</h4>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($summary['balances'] as $balance)
                        <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 bg-gray-50">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{
                                $balance['net_balance'] > 0 ? 'bg-green-100' : ($balance['net_balance'] < 0 ? 'bg-red-100' : 'bg-gray-200')
                            }}">
                                <span class="text-lg font-bold {{
                                    $balance['net_balance'] > 0 ? 'text-green-600' : ($balance['net_balance'] < 0 ? 'text-red-600' : 'text-gray-500')
                                }}">
                                    {{ strtoupper(substr($balance['friend']->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold text-gray-900">{{ $balance['friend']->name }}</span>
                                    @if($balance['net_balance'] > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Should Receive</span>
                                    @elseif($balance['net_balance'] < 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Should Pay</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Settled</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    Paid: <span class="font-medium text-gray-700">${{ number_format($balance['paid'], 2) }}</span> &nbsp;|
                                    Owes: <span class="font-medium text-gray-700">${{ number_format($balance['should_pay'], 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-2 text-lg font-bold {{
                                $balance['net_balance'] > 0 ? 'text-green-600' : ($balance['net_balance'] < 0 ? 'text-red-600' : 'text-gray-500')
                            }}">
                                {{ $balance['net_balance'] > 0 ? '+' : ($balance['net_balance'] < 0 ? '-' : '') }}${{ number_format(abs($balance['net_balance']), 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Settlement Recommendations -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-2">Optimal Settlement Plan</h4>
                @if(!empty($summary['settlement']['transactions']))
                    <ul class="space-y-2">
                        @foreach($summary['settlement']['transactions'] as $txn)
                            <li class="flex items-center space-x-2">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center bg-red-100 text-red-600 font-bold">
                                    {{ strtoupper(substr($txn['from']->name, 0, 1)) }}
                                </span>
                                <span class="text-gray-700 font-medium">{{ $txn['from']->name }}</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <span class="w-8 h-8 rounded-full flex items-center justify-center bg-green-100 text-green-600 font-bold">
                                    {{ strtoupper(substr($txn['to']->name, 0, 1)) }}
                                </span>
                                <span class="text-gray-700 font-medium">{{ $txn['to']->name }}</span>
                                <span class="ml-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-semibold text-sm">${{ number_format($txn['amount'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-green-700 bg-green-50 border border-green-200 rounded-md px-4 py-3">
                        <span class="font-semibold">No transactions needed.</span>
                        <span class="ml-2">Everyone is settled up!</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bill Overview -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Friends</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $bill->friends->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Expenses</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $bill->expenses->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Spent</p>
                    <p class="text-lg font-semibold text-gray-900">${{ number_format($bill->expenses->sum('amount'), 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <p class="text-lg font-semibold {{ $bill->status === 'settled' ? 'text-green-600' : 'text-gray-900' }}">
                        {{ ucfirst($bill->status) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

   


    <!-- Expenses List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ showExpenseModal: false }">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Expenses</h3>
            @if($bill->status === 'active')
                <button @click="showExpenseModal = true" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Expense
                </button>
            @else
                <span class="text-sm text-gray-500 italic">Bill is settled - no more expenses can be added</span>
            @endif
        </div>
        
        @if($bill->expenses->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($bill->expenses as $expense)
                    <div class="p-6 transition-all duration-300 hover:bg-gray-50" x-data="{ showConfirm: false }">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $expense->title }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ${{ number_format($expense->amount, 2) }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                    <span>Paid by <span class="font-medium text-gray-900">{{ $expense->paidBy->name }}</span></span>
                                    <span>•</span>
                                    <span>{{ $expense->sharedBy->count() }} {{ Str::plural('person', $expense->sharedBy->count()) }} sharing</span>
                                    <span>•</span>
                                    <span>{{ $expense->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($expense->sharedBy as $sharingFriend)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $sharingFriend->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            
                            @if($bill->status === 'active')
                                <div class="ml-4 flex-shrink-0">
                                    <form action="{{ route('bills.expenses.destroy', [$bill, $expense]) }}" method="POST" class="inline" @submit.prevent="showConfirm = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-full" aria-label="Delete expense">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        <!-- Alpine.js Modal for confirmation -->
                                        <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                                            <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
                                                <h2 class="text-lg font-semibold mb-2 text-gray-900">Delete Expense?</h2>
                                                <p class="text-gray-600 mb-4">Are you sure you want to delete <span class="font-bold">{{ $expense->title }}</span>? This action cannot be undone.</p>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" @click="showConfirm = false" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">Cancel</button>
                                                    <button type="submit" @click.stop="showConfirm = false; $el.closest('form').submit();" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No expenses yet</h3>
                <p class="text-gray-500">Add your first expense to start tracking shared costs.</p>
            </div>
        @endif

        <!-- Add Expense Modal -->
        <div x-show="showExpenseModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
             @click.away="showExpenseModal = false"
             @keydown.escape.window="showExpenseModal = false">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Add New Expense</h3>
                    <button @click="showExpenseModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('bills.expenses.store', $bill) }}" method="POST" class="p-6 space-y-6" x-data="expenseForm()" @submit.prevent="submitExpense($el)">
                    @csrf
                    
                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Expense Title -->
                        <div class="sm:col-span-2">
                            <label for="modal-title" class="block text-sm font-medium text-gray-700 mb-2">
                                Expense Title
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="modal-title" 
                                   value="{{ old('title') }}"
                                   placeholder="e.g., Dinner, Gas, Hotel"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-300 @enderror"
                                   required autofocus>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="modal-amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Amount ($)
                            </label>
                            <input type="number" 
                                   name="amount" 
                                   id="modal-amount" 
                                   value="{{ old('amount') }}"
                                   step="0.01" 
                                   min="0.01"
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-300 @enderror"
                                   required>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Paid By -->
                        <div>
                            <label for="modal-paid-by" class="block text-sm font-medium text-gray-700 mb-2">
                                Paid By
                            </label>
                            <select name="paid_by" 
                                    id="modal-paid-by"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('paid_by') border-red-300 @enderror"
                                    required>
                                <option value="">Select who paid</option>
                                @foreach($bill->friends as $friend)
                                    <option value="{{ $friend->id }}" {{ old('paid_by') == $friend->id ? 'selected' : '' }}>
                                        {{ $friend->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('paid_by')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Shared By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Shared By
                        </label>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($bill->friends as $friend)
                                <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-150 focus-within:ring-2 focus-within:ring-blue-500">
                                    <input type="checkbox" 
                                           name="shared_by[]" 
                                           value="{{ $friend->id }}"
                                           {{ in_array($friend->id, old('shared_by', [])) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           aria-label="Share with {{ $friend->name }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600">
                                                {{ strtoupper(substr($friend->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $friend->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('shared_by')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                @click="showExpenseModal = false"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="loading"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span x-show="!loading">Add Expense</span>
                            <span x-show="loading">Adding...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function expenseForm() {
    return {
        loading: false,
        submitExpense(form) {
            this.loading = true;
            form.submit();
        }
    }
}
</script>
@endpush 