<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.save');
