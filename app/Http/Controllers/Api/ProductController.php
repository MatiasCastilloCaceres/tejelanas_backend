<?php
/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Producto de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lana Natural Merino"),
 *     @OA\Property(property="description", type="string", example="Lana natural de alta calidad"),
 *     @OA\Property(property="price", type="number", format="float", example=15990.50),
 *     @OA\Property(property="stock", type="integer", example=25),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="weight", type="number", format="float", example=100.0),
 *     @OA\Property(property="color", type="string", example="Azul marino"),
 *     @OA\Property(property="material", type="string", example="100% Lana Merino"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
 * )
 *
 * @OA\Tag(
 *     name="Products",
 *     description="Operaciones CRUD para productos de Tejelanas Vivi"
 * )
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Operaciones CRUD para productos de Tejelanas Vivi"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Obtener lista de productos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Productos obtenidos exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Product")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        $perPage = min($request->get('per_page', 15), 100);
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate($perPage);
        $responseTime = round((microtime(true) - $startTime), 3);

        return response()->json([
            'success' => true,
            'message' => 'Productos obtenidos exitosamente',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ],
            'response_time' => $responseTime
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     tags={"Products"},
     *     summary="Crear un nuevo producto",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=201, description="Producto creado exitosamente")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'nullable|url',
                'weight' => 'nullable|numeric|min:0',
                'color' => 'nullable|string|max:100',
                'material' => 'nullable|string|max:255',
                'status' => 'in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            $product = Product::create($validated);
            $product->load('category');

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $product,
                'response_time' => $responseTime
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Obtener un producto específico",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producto obtenido exitosamente")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Actualizar un producto",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producto actualizado")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'status' => 'in:active,inactive'
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     tags={"Products"},
     *     summary="Eliminar un producto",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producto eliminado")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products-services",
     *     tags={"Products"},
     *     summary="Obtener productos y servicios (endpoint compatible)",
     *     description="Endpoint compatible con sistema existente de Tejelanas Vivi",
     *     @OA\Response(
     *         response=200,
     *         description="Productos y servicios obtenidos exitosamente"
     *     )
     * )
     */
    public function productsServices(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Obtener productos activos
            $products = Product::with('category')
                ->active()
                ->select(['id', 'name', 'description', 'price', 'stock', 'category_id', 'image_url', 'material', 'color'])
                ->orderBy('name')
                ->get();

            // Obtener talleres próximos como "servicios"
            $workshops = Workshop::upcoming()
                ->available()
                ->select(['id', 'title as name', 'description', 'price', 'date', 'time', 'duration', 'max_participants', 'current_participants'])
                ->orderBy('date')
                ->limit(10)
                ->get();

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Productos y servicios obtenidos exitosamente',
                'data' => [
                    'products' => $products,
                    'services' => $workshops,
                    'categories' => Category::active()->get(['id', 'name']),
                    'featured' => $products->where('stock', '>', 10)->take(6)->values()
                ],
                'response_time' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en ProductController@productsServices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}