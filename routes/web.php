<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Load trader routes
require __DIR__ . '/trader.php';

// Load trader routes
require __DIR__ . '/trader.php';
