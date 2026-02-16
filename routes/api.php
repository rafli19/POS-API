<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('v1')->group(function () {

    // Public Routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Admin, Owner, Kasir - READ ONLY
        Route::middleware('role:admin,owner,kasir')->group(function () {
            Route::get('/payment-methods', function () {
                $methods = \App\Models\PaymentMethod::active()->orderBy('type')->get();
                return response()->json([
                    'success' => true,
                    'data' => $methods
                ]);
            });

            // Dashboard
            Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
            Route::get('/dashboard/notifications', [DashboardController::class, 'notifications']);
            Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData']);
            Route::get('/dashboard/quick-stats', [DashboardController::class, 'quickStats']);

            // Categories - READ ONLY
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::get('/categories/{id}', [CategoryController::class, 'show']);

            // Products - READ ONLY
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{id}', [ProductController::class, 'show']);
            Route::get('/products/low-stock', [ProductController::class, 'lowStock']);
            Route::get('/products/statistics', [ProductController::class, 'statistics']);

            // Transactions - READ ONLY
            Route::get('/transactions/statistics', [TransactionController::class, 'statistics']);
            Route::get('/transactions/daily-report', [TransactionController::class, 'dailyReport']);
            Route::get('/transactions', [TransactionController::class, 'index']);
            Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        });

        // Admin & Owner Only
        Route::middleware('role:admin,owner')->group(function () {
            // Reports
            Route::get('/reports/sales', [ReportController::class, 'salesReport']);
            Route::get('/reports/stock', [ReportController::class, 'stockReport']);
            Route::get('/reports/transactions', [ReportController::class, 'transactionReport']);
            Route::get('/reports/kasir', [ReportController::class, 'kasirReport']);
        });

        // Admin Only
        Route::middleware('role:admin')->group(function () {
            // Products CRUD
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{id}', [ProductController::class, 'update']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);

            // Categories CRUD
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

            // Transactions (Update & Delete)
            Route::put('/transactions/{id}', [TransactionController::class, 'update']);
            Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);
        });

        // Kasir Only
        Route::middleware('role:kasir')->group(function () {
            // Create Transaction
            Route::post('/transactions', [TransactionController::class, 'store']);
            
            // Payment Verification
            Route::post('/transactions/{id}/confirm', [TransactionController::class, 'confirmPayment']);
            Route::post('/transactions/{id}/cancel', [TransactionController::class, 'cancelPayment']);
        });
    });
});