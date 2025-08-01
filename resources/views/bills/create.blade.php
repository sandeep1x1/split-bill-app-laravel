@extends('layouts.app')

@section('title', 'Create New Bill')

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
            Create Bill
        </span>
    </li>
</x-breadcrumbs>
<div class="max-w-2xl mx-auto">
    <!-- Mobile Back Button -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 rounded-md text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Back to dashboard">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </a>
    </div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full" aria-label="Back to dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New Bill</h1>
        </div>
        <p class="text-gray-600">Set up a new bill and add friends to split expenses with. <span class="block text-xs text-gray-400 mt-1">Tip: You can add or remove friends dynamically below. Each friend name must be unique.</span></p>
    </div>

    <!-- Create Bill Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('bills.store') }}" method="POST" class="p-6 space-y-6" x-data="billForm()" @submit.prevent="submitForm($el)">
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
                       required autofocus>
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
                
                <template x-for="(friend, idx) in friends" :key="idx">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="flex-1">
                            <input type="text" 
                                   :name="'friends['+idx+']'"
                                   x-model="friends[idx]"
                                   placeholder="Friend's name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required @keydown.enter.prevent="addFriend()">
                        </div>
                        <button type="button" @click="removeFriend(idx)" x-show="friends.length > 1"
                                class="remove-friend-btn inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </template>
                <button type="button" @click="addFriend()"
                        class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Friend
                </button>
                <template x-if="duplicateError">
                    <p class="mt-2 text-sm text-red-600">Duplicate friend names are not allowed.</p>
                </template>
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
                        :disabled="loading"
                        class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span x-show="!loading">Create Bill</span>
                    <span x-show="loading">Creating...</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function billForm() {
    return {
        friends: @json(old('friends', [''])),
        loading: false,
        duplicateError: false,
        addFriend() {
            this.friends.push('');
            this.$nextTick(() => {
                const inputs = document.querySelectorAll('input[name^=friends]');
                if (inputs.length) inputs[inputs.length - 1].focus();
            });
        },
        removeFriend(idx) {
            if (this.friends.length > 1) {
                this.friends.splice(idx, 1);
            }
        },
        submitForm(form) {
            this.duplicateError = false;
            const trimmed = this.friends.map(f => f.trim().toLowerCase());

            const unique = [...new Set(trimmed)];
            if (trimmed.length !== unique.length) {
                this.duplicateError = true;
                return;
            }
            this.loading = true;
            this.$root.loading = true;
            form.submit();
        }
    }
}
</script>
@endpush
@endsection 