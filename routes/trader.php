<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TraderAuthController;
use App\Http\Controllers\StrategyController;

// Trader authentication routes (public)
Route::get('trader/login', [TraderAuthController::class, 'showLoginForm'])->name('trader.login');
Route::post('trader/login', [TraderAuthController::class, 'login'])->name('trader.login.post');
Route::get('trader/register', [TraderAuthController::class, 'showRegisterForm'])->name('trader.register');
Route::post('trader/register', [TraderAuthController::class, 'register'])->name('trader.register.post');

// Email verification routes
Route::middleware('auth:trader')->group(function () {
    Route::get('trader/verify-email', [TraderAuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::post('trader/resend-verification', [TraderAuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::get('trader/verify-email/{id}/{hash}', [TraderAuthController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Dashboard route (allow unverified access but will show verification notice)
Route::middleware('auth:trader')->group(function () {
    Route::get('trader/dashboard', [StrategyController::class, 'dashboard'])->name('trader.dashboard');
    Route::post('trader/logout', [TraderAuthController::class, 'logout'])->name('trader.logout');
});

// Strategy routes (require email verification)
Route::middleware('auth:trader', 'verified')->group(function () {
    // Strategy routes
    Route::get('trader/strategy/create', [StrategyController::class, 'createForm'])->name('trader.strategy.create');
    Route::post('trader/strategy', [StrategyController::class, 'store'])->name('trader.strategy.store');
    Route::get('trader/strategy/{strategy}', [StrategyController::class, 'show'])->name('trader.strategy.show');
    Route::post('trader/strategy/{strategy}/transaction', [StrategyController::class, 'recordTransaction'])->name('trader.strategy.transaction');
    Route::post('trader/strategy/{strategy}/sip-reminder', [StrategyController::class, 'generateSIPReminder'])->name('trader.strategy.sip-reminder');
    Route::get('trader/strategy/{strategy}/journal', [StrategyController::class, 'journal'])->name('trader.strategy.journal');
    Route::get('trader/strategy/{strategy}/dip-eligibility', [StrategyController::class, 'dipEligibility'])->name('trader.strategy.dip-eligibility');
});
