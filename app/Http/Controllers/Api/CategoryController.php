<?php
/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Categoría de productos de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lanas Premium"),
 *     @OA\Property(property="description", type="string", example="Lanas de alta calidad importadas"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="products_count", type="integer", example=15, description="Número de productos en esta categoría"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="Categories",
 *     description="Operaciones CRUD para categorías de productos"
 * )
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Operaciones CRUD para categorías de productos"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Obtener lista de categorías",
     *     description="Obtiene una lista paginada de categorías con conteo de productos y filtros avanzados",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Elementos por página (máximo 100)",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filtrar por estado",
     *         @OA\Schema(type="string", enum={"active", "inactive"}, example="active")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Buscar en nombre y descripción",
     *         @OA\Schema(type="string", example="lana")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categorías obtenidas exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Category")
     *                 ),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=3),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=45)
     *                 )
     *             ),
     *             @OA\Property(property="response_time", type="number", format="float", example=0.025)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Validación robusta de parámetros
            $validated = $request->validate([
                'per_page' => 'integer|min:1|max:100',
                'status' => 'in:active,inactive',
                'search' => 'string|max:255',
                'sort_by' => 'in:name,created_at,products_count',
                'sort_order' => 'in:asc,desc'
            ]);

            $perPage = $validated['per_page'] ?? 15;
            $sortBy = $validated['sort_by'] ?? 'name';
            $sortOrder = $validated['sort_order'] ?? 'asc';

            // Query optimizada con conteo de productos
            $query = Category::withCount('products')
                ->select(['id', 'name', 'description', 'status', 'created_at', 'updated_at']);

            // Aplicar filtros dinámicos
            if (isset($validated['status'])) {
                $query->where('status', $validated['status']);
            }

            if (isset($validated['search'])) {
                $searchTerm = $validated['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Aplicar ordenamiento
            if ($sortBy === 'products_count') {
                $query->orderBy('products_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Ejecutar consulta paginada
            $categories = $query->paginate($perPage);

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Categorías obtenidas exitosamente',
                'data' => [
                    'categories' => $categories->items(),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
                        'from' => $categories->firstItem(),
                        'to' => $categories->lastItem()
                    ]
                ],
                'response_time' => $responseTime
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en CategoryController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     tags={"Categories"},
     *     summary="Crear nueva categoría",
     *     description="Crea una nueva categoría de productos con validaciones robustas",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Lanas Alpaca"),
     *             @OA\Property(property="description", type="string", maxLength=1000, example="Lanas de alpaca 100% naturales"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categoría creada exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category"),
     *             @OA\Property(property="response_time", type="number", format="float", example=0.045)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Validación robusta
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'description' => 'nullable|string|max:1000',
                'status' => 'in:active,inactive'
            ], [
                'name.required' => 'El nombre de la categoría es obligatorio',
                'name.unique' => 'Ya existe una categoría con este nombre',
                'name.max' => 'El nombre no puede exceder 255 caracteres',
                'description.max' => 'La descripción no puede exceder 1000 caracteres'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';

            // Crear categoría con transacción
            \DB::beginTransaction();

            $category = Category::create($validated);
            $category->loadCount('products');

            \DB::commit();

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $category,
                'response_time' => $responseTime
            ], 201);

        } catch (ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (QueryException $e) {
            \DB::rollBack();
            \Log::error('Error de base de datos en CategoryController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la base de datos',
                'error' => 'No se pudo crear la categoría'
            ], 500);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error general en CategoryController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Obtener categoría específica",
     *     description="Obtiene una categoría específica con opción de incluir sus productos",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="include_products",
     *         in="query",
     *         required=false,
     *         description="Incluir productos activos de la categoría",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Categoría no encontrada")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Validar ID
            if (!is_numeric($id) || $id < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de categoría inválido'
                ], 400);
            }

            $query = Category::withCount('products');

            // Incluir productos si se solicita
            if ($request->boolean('include_products')) {
                $query->with([
                    'products' => function ($q) {
                        $q->active()
                            ->select(['id', 'name', 'price', 'stock', 'category_id', 'status'])
                            ->orderBy('name');
                    }
                ]);
            }

            $category = $query->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error en CategoryController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Actualizar categoría",
     *     description="Actualiza una categoría existente con validaciones robustas",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", maxLength=1000),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Categoría actualizada exitosamente")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'status' => 'in:active,inactive'
            ], [
                'name.required' => 'El nombre de la categoría es obligatorio',
                'name.unique' => 'Ya existe una categoría con este nombre',
                'name.max' => 'El nombre no puede exceder 255 caracteres'
            ]);

            // Transacción para actualización
            \DB::beginTransaction();

            $category->update($validated);
            $category->loadCount('products');

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $category
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error en CategoryController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar categoría'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{id}",
     *     tags={"Categories"},
     *     summary="Eliminar categoría",
     *     description="Elimina una categoría si no tiene productos asociados",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categoría eliminada exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="No se puede eliminar: tiene productos asociados",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar: la categoría tiene productos asociados"),
     *             @OA\Property(property="products_count", type="integer", example=5)
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Verificar productos asociados con conteo eficiente
            $productsCount = $category->products()->count();

            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar: la categoría tiene productos asociados',
                    'products_count' => $productsCount
                ], 409);
            }

            // Eliminar con transacción
            \DB::beginTransaction();
            $category->delete();
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error en CategoryController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar categoría'
            ], 500);
        }
    }
}