<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('products', ProductController::class)->only([
        'index',
        'store',
        'update',
        'destroy',
    ]);
});
