<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Documentation",
 *     description="Información sobre la API y rendimiento"
 * )
 */
class DocumentationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/performance",
     *     tags={"Documentation"},
     *     summary="Información de rendimiento de la API",
     *     description="Retorna información sobre rendimiento, rate limiting y optimización",
     *     @OA\Response(
     *         response=200,
     *         description="Información de rendimiento obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Información de rendimiento obtenida"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="rate_limiting",
     *                     type="object",
     *                     @OA\Property(property="limit", type="integer", example=100, description="Requests por minuto"),
     *                     @OA\Property(property="window", type="string", example="1 minute", description="Ventana de tiempo"),
     *                     @OA\Property(property="headers", type="array", @OA\Items(type="string"), example={"X-RateLimit-Limit", "X-RateLimit-Remaining", "X-RateLimit-Reset"})
     *                 ),
     *                 @OA\Property(
     *                     property="caching",
     *                     type="object",
     *                     @OA\Property(property="ttl_get_requests", type="string", example="10 minutes", description="TTL para requests GET"),
     *                     @OA\Property(property="cache_key_format", type="string", example="api_cache:md5(url)", description="Formato de clave de cache"),
     *                     @OA\Property(property="headers", type="array", @OA\Items(type="string"), example={"X-Cache", "X-Cache-Key", "X-Cache-TTL"})
     *                 ),
     *                 @OA\Property(
     *                     property="response_times",
     *                     type="object",
     *                     @OA\Property(property="expected_get", type="string", example="< 150ms", description="Tiempo esperado para GET"),
     *                     @OA\Property(property="expected_post", type="string", example="< 300ms", description="Tiempo esperado para POST"),
     *                     @OA\Property(property="expected_put", type="string", example="< 250ms", description="Tiempo esperado para PUT"),
     *                     @OA\Property(property="expected_delete", type="string", example="< 200ms", description="Tiempo esperado para DELETE")
     *                 ),
     *                 @OA\Property(
     *                     property="optimization_tips",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={
     *                         "Use pagination with per_page parameter (max 100)",
     *                         "Filter results using query parameters",
     *                         "Cache GET requests are served from cache for 10 minutes",
     *                         "Include only necessary fields in requests",
     *                         "Use bulk operations when possible"
     *                     }
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function performance(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Información de rendimiento obtenida',
            'data' => [
                'rate_limiting' => [
                    'limit' => 100,
                    'window' => '1 minute',
                    'headers' => [
                        'X-RateLimit-Limit',
                        'X-RateLimit-Remaining',
                        'X-RateLimit-Reset'
                    ]
                ],
                'caching' => [
                    'ttl_get_requests' => '10 minutes',
                    'cache_key_format' => 'api_cache:md5(url)',
                    'headers' => [
                        'X-Cache',
                        'X-Cache-Key',
                        'X-Cache-TTL'
                    ]
                ],
                'response_times' => [
                    'expected_get' => '< 150ms',
                    'expected_post' => '< 300ms',
                    'expected_put' => '< 250ms',
                    'expected_delete' => '< 200ms'
                ],
                'optimization_tips' => [
                    'Use pagination with per_page parameter (max 100)',
                    'Filter results using query parameters',
                    'Cache GET requests are served from cache for 10 minutes',
                    'Include only necessary fields in requests',
                    'Use bulk operations when possible',
                    'Avoid unnecessary requests by using cached data',
                    'Use specific filters to reduce response size'
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/examples",
     *     tags={"Documentation"},
     *     summary="Ejemplos de uso de la API",
     *     description="Retorna ejemplos de diferentes tipos de datos y estructuras",
     *     @OA\Response(
     *         response=200,
     *         description="Ejemplos obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ejemplos de uso obtenidos"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="product_examples",
     *                     type="object",
     *                     @OA\Property(
     *                         property="basic_product",
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="Lana Merino Básica"),
     *                         @OA\Property(property="price", type="number", example=15990.50),
     *                         @OA\Property(property="stock", type="integer", example=25),
     *                         @OA\Property(property="category_id", type="integer", example=1)
     *                     ),
     *                     @OA\Property(
     *                         property="complete_product",
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="Lana Alpaca Premium"),
     *                         @OA\Property(property="description", type="string", example="Lana de alpaca premium, suave y cálida"),
     *                         @OA\Property(property="price", type="number", example=24990.00),
     *                         @OA\Property(property="stock", type="integer", example=15),
     *                         @OA\Property(property="category_id", type="integer", example=1),
     *                         @OA\Property(property="weight", type="number", example=100.0),
     *                         @OA\Property(property="color", type="string", example="Natural"),
     *                         @OA\Property(property="material", type="string", example="100% Alpaca"),
     *                         @OA\Property(property="status", type="string", example="active")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="workshop_examples",
     *                     type="object",
     *                     @OA\Property(
     *                         property="basic_workshop",
     *                         type="object",
     *                         @OA\Property(property="title", type="string", example="Crochet Básico"),
     *                         @OA\Property(property="date", type="string", example="2025-07-15"),
     *                         @OA\Property(property="time", type="string", example="14:00"),
     *                         @OA\Property(property="duration", type="integer", example=120),
     *                         @OA\Property(property="price", type="number", example=25000.00),
     *                         @OA\Property(property="max_participants", type="integer", example=8)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="filter_examples",
     *                     type="object",
     *                     @OA\Property(property="products_by_category", type="string", example="/api/v1/products?category_id=1"),
     *                     @OA\Property(property="active_products", type="string", example="/api/v1/products?status=active"),
     *                     @OA\Property(property="products_pagination", type="string", example="/api/v1/products?page=2&per_page=20"),
     *                     @OA\Property(property="workshops_by_date", type="string", example="/api/v1/workshops?date_from=2025-06-01&date_to=2025-12-31")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function examples(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Ejemplos de uso obtenidos',
            'data' => [
                'product_examples' => [
                    'basic_product' => [
                        'name' => 'Lana Merino Básica',
                        'price' => 15990.50,
                        'stock' => 25,
                        'category_id' => 1
                    ],
                    'complete_product' => [
                        'name' => 'Lana Alpaca Premium',
                        'description' => 'Lana de alpaca premium, suave y cálida',
                        'price' => 24990.00,
                        'stock' => 15,
                        'category_id' => 1,
                        'weight' => 100.0,
                        'color' => 'Natural',
                        'material' => '100% Alpaca',
                        'status' => 'active'
                    ]
                ],
                'workshop_examples' => [
                    'basic_workshop' => [
                        'title' => 'Crochet Básico',
                        'date' => '2025-07-15',
                        'time' => '14:00',
                        'duration' => 120,
                        'price' => 25000.00,
                        'max_participants' => 8
                    ]
                ],
                'filter_examples' => [
                    'products_by_category' => '/api/v1/products?category_id=1',
                    'active_products' => '/api/v1/products?status=active',
                    'products_pagination' => '/api/v1/products?page=2&per_page=20',
                    'workshops_by_date' => '/api/v1/workshops?date_from=2025-06-01&date_to=2025-12-31'
                ]
            ]
        ]);
    }
}