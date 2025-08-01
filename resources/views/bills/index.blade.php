@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<x-breadcrumbs>
    <li>
        <span class="inline-flex items-center text-blue-600 font-semibold">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18"></path>
            </svg>
            Dashboard
        </span>
    </li>
</x-breadcrumbs>
<div class="space-y-6">
    <!-- Header -->
    <div class="text-center sm:text-left">
        <h1 class="text-3xl font-bold text-gray-900 sm:text-4xl">Split Bills</h1>
        <p class="mt-2 text-gray-600">Manage and track shared expenses with friends</p>
        <p class="text-xs text-gray-400 mt-1">Tip: Click "Create New Bill" to start a new group, or select a bill to view details and settlements.</p>
    </div>

    <!-- Create New Bill Button -->
    <div class="flex justify-center sm:justify-start">
        <a href="{{ route('bills.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Bill
        </a>
    </div>

    <!-- Bills List -->
    <div class="space-y-4">
        @if($bills->count() > 0)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($bills as $bill)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        {{ $bill->name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $bill->friends->count() }} {{ Str::plural('friend', $bill->friends->count()) }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $bill->expenses->count() }} {{ Str::plural('expense', $bill->expenses->count()) }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Created {{ $bill->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bill->status === 'settled' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @foreach($bill->friends->take(3) as $friend)
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-600">
                                                {{ strtoupper(substr($friend->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($bill->friends->count() > 3)
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-500">
                                                +{{ $bill->friends->count() - 3 }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('bills.show', $bill) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    View Details
                                    <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bills yet</h3>
                <p class="text-gray-500 mb-6">Get started by creating your first bill to split expenses with friends.</p>
                <a href="{{ route('bills.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Your First Bill
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 