<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (): array {
    return [
        'status' => 'ok',
        'service' => 'invoice-api',
    ];
});

Route::apiResource('invoices', InvoiceController::class)->only([
    'index',
    'store',
    'show',
]);
