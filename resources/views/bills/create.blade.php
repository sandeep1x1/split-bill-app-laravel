@extends('layouts.app')

@section('title', 'Create New Bill')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New Bill</h1>
        </div>
        <p class="text-gray-600">Set up a new bill and add friends to split expenses with.</p>
    </div>

    <!-- Create Bill Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('bills.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Bill Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Bill Name
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       placeholder="e.g., Goa Trip, Dinner at XYZ, Weekend Getaway"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Friends Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Friends
                </label>
                <p class="text-sm text-gray-500 mb-4">Add friends who will be splitting expenses with you.</p>
                
                <div id="friends-container" class="space-y-3">
                    <!-- Friend inputs will be added here -->
                </div>

                <!-- Add Friend Button -->
                <button type="button" 
                        id="add-friend-btn"
                        class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Friend
                </button>

                @error('friends')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('friends.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('home') }}" 
                   class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Bill
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const friendsContainer = document.getElementById('friends-container');
    const addFriendBtn = document.getElementById('add-friend-btn');
    let friendCount = 0;

    // Add initial friend input
    addFriendInput();

    // Add friend button click handler
    addFriendBtn.addEventListener('click', addFriendInput);

    function addFriendInput() {
        friendCount++;
        const friendDiv = document.createElement('div');
        friendDiv.className = 'flex items-center space-x-3';
        friendDiv.innerHTML = `
            <div class="flex-1">
                <input type="text" 
                       name="friends[]" 
                       placeholder="Friend's name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>
            <button type="button" 
                    class="remove-friend-btn inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 ${friendCount === 1 ? 'hidden' : ''}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;

        friendsContainer.appendChild(friendDiv);

        // Add remove button functionality
        const removeBtn = friendDiv.querySelector('.remove-friend-btn');
        removeBtn.addEventListener('click', function() {
            friendDiv.remove();
            updateRemoveButtons();
        });

        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-friend-btn');
        removeButtons.forEach((btn, index) => {
            if (removeButtons.length === 1) {
                btn.classList.add('hidden');
            } else {
                btn.classList.remove('hidden');
            }
        });
    }
});
</script>
@endpush
@endsection 