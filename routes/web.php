<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;

// 1. API Route for polling (Must be before the resource route)
Route::get('/api/dashboard/stats', [DashboardController::class, 'stats']);

// 2. Dashboard Route
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 3. Resource Route
Route::resource('transactions', TransactionController::class);