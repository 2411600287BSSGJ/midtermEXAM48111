<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('transactions', TransactionController::class);