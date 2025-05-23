<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FacturePdfController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.save');

Route::get('/facture/pdf/{id}', [FacturePdfController::class, 'generate'])->name('preview-pdf.pdf');

