<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FacturePdfController;
use App\Exports\LowStockCsvService;
use App\Http\Controllers\ExportPdfController;



Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.save');

Route::get('/facture/pdf/{id}', [FacturePdfController::class, 'generate'])->name('preview-pdf.pdf');

Route::get('/devis/pdf/{id}', [FacturePdfController::class, 'generateQuote'])->name('preview-quote-pdf.pdf');

Route::get('/products/export-low-stock', [ExportPdfController::class, 'downloadLowStockPdf'])
     ->name('products.export.lowstock');


