<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\FaqController;

/*
|--------------------------------------------------------------------------
| API Routes - Tejelanas Vivi (Compatible con endpoints existentes)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Rutas públicas (sin autenticación)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/workshops', [WorkshopController::class, 'index']);
    Route::get('/workshops/{id}', [WorkshopController::class, 'show']);

    // Rutas equivalentes a los endpoints existentes
    Route::get('/products-services', [ProductController::class, 'productsServices']);
    Route::get('/about-us', [AboutUsController::class, 'index']);
    Route::get('/faq', [FaqController::class, 'index']);

    // Rutas protegidas con Bearer Token personalizado (ipss.get)
    Route::middleware('custom.bearer')->group(function () {

        // Products CRUD
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Categories CRUD
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Workshops CRUD
        Route::post('/workshops', [WorkshopController::class, 'store']);
        Route::put('/workshops/{id}', [WorkshopController::class, 'update']);
        Route::delete('/workshops/{id}', [WorkshopController::class, 'destroy']);

        // About Us CRUD
        Route::post('/about-us', [AboutUsController::class, 'store']);
        Route::put('/about-us/{id}', [AboutUsController::class, 'update']);
        Route::delete('/about-us/{id}', [AboutUsController::class, 'destroy']);

        // FAQ CRUD
        Route::post('/faq', [FaqController::class, 'store']);
        Route::put('/faq/{id}', [FaqController::class, 'update']);
        Route::delete('/faq/{id}', [FaqController::class, 'destroy']);

        // Rutas administrativas adicionales
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
        Route::post('/admin/bulk-update', [AdminController::class, 'bulkUpdate']);
    });
});

// Ruta de prueba de autenticación
Route::middleware('custom.bearer')->get('/v1/auth-test', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Autenticación exitosa',
        'token_used' => $request->bearerToken(),
        'timestamp' => now()
    ]);
});