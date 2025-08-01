<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

// Root route - display all bills (dashboard)
Route::get('/', [BillController::class, 'index'])->name('home');

// Bill resource routes
Route::resource('bills', BillController::class);

// Nested expense routes under bills
Route::post('/bills/{bill}/expenses', [ExpenseController::class, 'store'])->name('bills.expenses.store');
Route::put('/bills/{bill}/expenses/{expense}', [ExpenseController::class, 'update'])->name('bills.expenses.update');
Route::delete('/bills/{bill}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('bills.expenses.destroy');

// Bill settlement route
Route::post('/bills/{bill}/settle', [BillController::class, 'settle'])->name('bills.settle');
