<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TejelanasProxyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas proxy para consultar APIs externas
Route::get('/products-services', [TejelanasProxyController::class, 'getProductsServices']);
Route::get('/about-us', [TejelanasProxyController::class, 'getAboutUs']);
Route::get('/faq', [TejelanasProxyController::class, 'getFaq']);

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Tejelanas Vivi funcionando correctamente',
        'timestamp' => now(),
        'endpoints' => [
            'products-services' => '/api/products-services',
            'about-us' => '/api/about-us',
            'faq' => '/api/faq'
        ]
    ])->header('Access-Control-Allow-Origin', '*');
});