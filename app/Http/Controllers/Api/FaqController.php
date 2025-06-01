<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="Faq",
 *     type="object",
 *     title="FAQ",
 *     description="Preguntas Frecuentes de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="question", type="string", example="¿Cuál es el tiempo de entrega?"),
 *     @OA\Property(property="answer", type="string", example="Los tiempos de entrega varían entre 3-5 días hábiles..."),
 *     @OA\Property(property="category", type="string", enum={"general", "productos", "talleres", "envios"}, example="envios"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="featured", type="boolean", example=true),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="views_count", type="integer", example=150),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="FAQ",
 *     description="Preguntas Frecuentes sobre Tejelanas Vivi"
 * )
 */
class FaqController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/faq",
     *     tags={"FAQ"},
     *     summary="Obtener preguntas frecuentes",
     *     description="Obtiene una lista de FAQs con filtros avanzados y optimización de rendimiento",
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         description="Filtrar por categoría",
     *         @OA\Schema(type="string", enum={"general", "productos", "talleres", "envios"})
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         required=false,
     *         description="Mostrar solo FAQs destacadas",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Buscar en pregunta y respuesta",
     *         @OA\Schema(type="string", example="entrega")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Elementos por página",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQs obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FAQs obtenidas exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="faqs",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Faq")
     *                 ),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="pagination", type="object")
     *             ),
     *             @OA\Property(property="response_time", type="number", format="float", example=0.032)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Validación avanzada de parámetros
            $validated = $request->validate([
                'category' => 'in:general,productos,talleres,envios',
                'featured' => 'boolean',
                'search' => 'string|max:255',
                'per_page' => 'integer|min:1|max:100',
                'sort_by' => 'in:question,order,views_count,created_at',
                'sort_order' => 'in:asc,desc'
            ]);

            $perPage = $validated['per_page'] ?? 20;
            $sortBy = $validated['sort_by'] ?? 'order';
            $sortOrder = $validated['sort_order'] ?? 'asc';

            // Query optimizada con selección específica de campos
            $query = Faq::active()
                ->select(['id', 'question', 'answer', 'category', 'order', 'featured', 'views_count', 'created_at', 'updated_at']);

            // Aplicar filtros dinámicos
            if (isset($validated['category'])) {
                $query->byCategory($validated['category']);
            }

            if (isset($validated['featured'])) {
                $query->where('featured', $validated['featured']);
            }

            if (isset($validated['search'])) {
                $searchTerm = $validated['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('question', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('answer', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Ordenamiento múltiple
            if ($sortBy === 'featured') {
                $query->orderBy('featured', 'desc')->orderBy('order', 'asc');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Ejecutar consulta paginada
            $faqs = $query->paginate($perPage);

            // Obtener categorías disponibles para el frontend
            $categories = Faq::getCategories();

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'FAQs obtenidas exitosamente',
                'data' => [
                    'faqs' => $faqs->items(),
                    'categories' => $categories,
                    'pagination' => [
                        'current_page' => $faqs->currentPage(),
                        'last_page' => $faqs->lastPage(),
                        'per_page' => $faqs->perPage(),
                        'total' => $faqs->total(),
                        'from' => $faqs->firstItem(),
                        'to' => $faqs->lastItem()
                    ]
                ],
                'response_time' => $responseTime
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en FaqController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/faq",
     *     tags={"FAQ"},
     *     summary="Crear nueva FAQ",
     *     description="Crea una nueva pregunta frecuente con validaciones robustas",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question", "answer"},
     *             @OA\Property(property="question", type="string", maxLength=255, example="¿Hacen envíos a regiones?"),
     *             @OA\Property(property="answer", type="string", example="Sí, realizamos envíos a todas las regiones de Chile..."),
     *             @OA\Property(property="category", type="string", enum={"general", "productos", "talleres", "envios"}, example="envios"),
     *             @OA\Property(property="order", type="integer", minimum=1, example=1),
     *             @OA\Property(property="featured", type="boolean", example=false),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="FAQ creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="FAQ creada exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Faq")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'category' => 'in:general,productos,talleres,envios',
                'order' => 'integer|min:1',
                'featured' => 'boolean',
                'status' => 'in:active,inactive'
            ], [
                'question.required' => 'La pregunta es obligatoria',
                'question.max' => 'La pregunta no puede exceder 255 caracteres',
                'answer.required' => 'La respuesta es obligatoria',
                'category.in' => 'La categoría debe ser: general, productos, talleres o envios'
            ]);

            // Valores por defecto
            $validated['category'] = $validated['category'] ?? 'general';
            $validated['order'] = $validated['order'] ?? 1;
            $validated['featured'] = $validated['featured'] ?? false;
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['views_count'] = 0;

            // Crear con transacción
            \DB::beginTransaction();

            $faq = Faq::create($validated);

            \DB::commit();

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'FAQ creada exitosamente',
                'data' => $faq,
                'response_time' => $responseTime
            ], 201);

        } catch (ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error en FaqController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear FAQ'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/faq/{id}",
     *     tags={"FAQ"},
     *     summary="Obtener FAQ específica",
     *     description="Obtiene una FAQ específica e incrementa el contador de vistas",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la FAQ",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Faq")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $faq = Faq::findOrFail($id);

            // Incrementar contador de vistas de forma asíncrona
            $faq->incrementViews();

            return response()->json([
                'success' => true,
                'data' => $faq
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error en FaqController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/faq/{id}",
     *     tags={"FAQ"},
     *     summary="Actualizar FAQ",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="FAQ actualizada exitosamente")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $faq = Faq::findOrFail($id);

            $validated = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'category' => 'in:general,productos,talleres,envios',
                'order' => 'integer|min:1',
                'featured' => 'boolean',
                'status' => 'in:active,inactive'
            ]);

            \DB::beginTransaction();

            $faq->update($validated);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'FAQ actualizada exitosamente',
                'data' => $faq
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ no encontrada'
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
            \Log::error('Error en FaqController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar FAQ'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/faq/{id}",
     *     tags={"FAQ"},
     *     summary="Eliminar FAQ",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="FAQ eliminada exitosamente")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $faq = Faq::findOrFail($id);

            \DB::beginTransaction();
            $faq->delete();
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'FAQ eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error en FaqController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar FAQ'
            ], 500);
        }
    }
}
