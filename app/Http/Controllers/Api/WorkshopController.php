<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *     schema="Workshop",
 *     type="object",
 *     title="Workshop",
 *     description="Taller de tejido de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Taller de Tejido a Dos Agujas - Nivel Básico"),
 *     @OA\Property(property="description", type="string", example="Aprende las técnicas básicas del tejido a dos agujas..."),
 *     @OA\Property(property="date", type="string", format="date", example="2024-07-15"),
 *     @OA\Property(property="time", type="string", format="time", example="14:30:00"),
 *     @OA\Property(property="duration", type="integer", example=180, description="Duración en minutos"),
 *     @OA\Property(property="price", type="number", format="float", example=25000.00),
 *     @OA\Property(property="max_participants", type="integer", example=8),
 *     @OA\Property(property="current_participants", type="integer", example=3),
 *     @OA\Property(property="available_spots", type="integer", example=5),
 *     @OA\Property(property="location", type="string", example="Taller Tejelanas Vivi - Sala Principal"),
 *     @OA\Property(property="instructor", type="string", example="Viviana González"),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/workshop.jpg"),
 *     @OA\Property(property="difficulty_level", type="string", enum={"principiante", "intermedio", "avanzado"}, example="principiante"),
 *     @OA\Property(property="materials_included", type="boolean", example=true),
 *     @OA\Property(property="requirements", type="string", example="No se requiere experiencia previa"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "full", "cancelled"}, example="active"),
 *     @OA\Property(property="status_display", type="string", example="Disponible"),
 *     @OA\Property(property="is_upcoming", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="Workshops",
 *     description="Talleres de tejido y manualidades"
 * )
 */
class WorkshopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/workshops",
     *     tags={"Workshops"},
     *     summary="Obtener lista de talleres",
     *     description="Obtiene talleres con filtros avanzados y optimización de rendimiento",
     *     @OA\Parameter(
     *         name="upcoming_only",
     *         in="query",
     *         required=false,
     *         description="Mostrar solo talleres próximos",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="available_only",
     *         in="query",
     *         required=false,
     *         description="Mostrar solo talleres disponibles",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="difficulty_level",
     *         in="query",
     *         required=false,
     *         description="Filtrar por nivel de dificultad",
     *         @OA\Schema(type="string", enum={"principiante", "intermedio", "avanzado"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         required=false,
     *         description="Fecha inicio (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-07-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         required=false,
     *         description="Fecha fin (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2024-07-31")
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         required=false,
     *         description="Precio máximo",
     *         @OA\Schema(type="number", example=30000)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Buscar en título y descripción",
     *         @OA\Schema(type="string", example="tejido")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Elementos por página",
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=12)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Talleres obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Talleres obtenidos exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="workshops",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Workshop")
     *                 ),
     *                 @OA\Property(property="filters", type="object",
     *                     @OA\Property(property="difficulty_levels", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="price_range", type="object",
     *                         @OA\Property(property="min", type="number"),
     *                         @OA\Property(property="max", type="number")
     *                     )
     *                 ),
     *                 @OA\Property(property="pagination", type="object")
     *             ),
     *             @OA\Property(property="response_time", type="number", format="float", example=0.045)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            // Validación robusta de filtros
            $validated = $request->validate([
                'upcoming_only' => 'boolean',
                'available_only' => 'boolean',
                'difficulty_level' => 'in:principiante,intermedio,avanzado',
                'date_from' => 'date|after_or_equal:today',
                'date_to' => 'date|after_or_equal:date_from',
                'price_max' => 'numeric|min:0',
                'search' => 'string|max:255',
                'per_page' => 'integer|min:1|max:100',
                'sort_by' => 'in:date,price,title,current_participants',
                'sort_order' => 'in:asc,desc'
            ]);

            $perPage = $validated['per_page'] ?? 12;
            $sortBy = $validated['sort_by'] ?? 'date';
            $sortOrder = $validated['sort_order'] ?? 'asc';

            // Query base optimizada
            $query = Workshop::select([
                'id',
                'title',
                'description',
                'date',
                'time',
                'duration',
                'price',
                'max_participants',
                'current_participants',
                'location',
                'instructor',
                'image_url',
                'difficulty_level',
                'materials_included',
                'requirements',
                'status',
                'created_at',
                'updated_at'
            ]);

            // Aplicar filtros dinámicos
            if ($validated['upcoming_only'] ?? false) {
                $query->upcoming();
            }

            if ($validated['available_only'] ?? false) {
                $query->available();
            }

            if (isset($validated['difficulty_level'])) {
                $query->byDifficulty($validated['difficulty_level']);
            }

            if (isset($validated['date_from'])) {
                $query->where('date', '>=', $validated['date_from']);
            }

            if (isset($validated['date_to'])) {
                $query->where('date', '<=', $validated['date_to']);
            }

            if (isset($validated['price_max'])) {
                $query->where('price', '<=', $validated['price_max']);
            }

            if (isset($validated['search'])) {
                $searchTerm = $validated['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('instructor', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Ordenamiento
            if ($sortBy === 'current_participants') {
                $query->orderByRaw('(max_participants - current_participants) DESC');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Ejecutar consulta paginada
            $workshops = $query->paginate($perPage);

            // Agregar campos calculados
            $workshops->getCollection()->transform(function ($workshop) {
                $workshop->available_spots = $workshop->availableSpots();
                $workshop->status_display = $workshop->status_display;
                $workshop->is_upcoming = $workshop->isUpcoming();
                return $workshop;
            });

            // Obtener datos para filtros del frontend
            $filters = [
                'difficulty_levels' => ['principiante', 'intermedio', 'avanzado'],
                'price_range' => [
                    'min' => Workshop::active()->min('price') ?? 0,
                    'max' => Workshop::active()->max('price') ?? 0
                ]
            ];

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Talleres obtenidos exitosamente',
                'data' => [
                    'workshops' => $workshops->items(),
                    'filters' => $filters,
                    'pagination' => [
                        'current_page' => $workshops->currentPage(),
                        'last_page' => $workshops->lastPage(),
                        'per_page' => $workshops->perPage(),
                        'total' => $workshops->total(),
                        'from' => $workshops->firstItem(),
                        'to' => $workshops->lastItem()
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
            \Log::error('Error en WorkshopController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/workshops",
     *     tags={"Workshops"},
     *     summary="Crear nuevo taller",
     *     description="Crea un nuevo taller con validaciones completas",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "date", "time", "duration", "price", "max_participants", "location"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Taller de Tejido Circular"),
     *             @OA\Property(property="description", type="string", example="Aprende las técnicas del tejido circular..."),
     *             @OA\Property(property="date", type="string", format="date", example="2024-08-15"),
     *             @OA\Property(property="time", type="string", format="time", example="10:00"),
     *             @OA\Property(property="duration", type="integer", minimum=30, maximum=480, example=120),
     *             @OA\Property(property="price", type="number", minimum=0, example=20000),
     *             @OA\Property(property="max_participants", type="integer", minimum=1, maximum=50, example=6),
     *             @OA\Property(property="location", type="string", example="Sala 2 - Tejelanas Vivi"),
     *             @OA\Property(property="instructor", type="string", example="María José Silva"),
     *             @OA\Property(property="image_url", type="string", format="url"),
     *             @OA\Property(property="difficulty_level", type="string", enum={"principiante", "intermedio", "avanzado"}),
     *             @OA\Property(property="materials_included", type="boolean", example=true),
     *             @OA\Property(property="requirements", type="string", example="Traer agujas de tejer N°4"),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Taller creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Taller creado exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Workshop")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date|after_or_equal:today',
                'time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:30|max:480',
                'price' => 'required|numeric|min:0',
                'max_participants' => 'required|integer|min:1|max:50',
                'location' => 'required|string|max:255',
                'instructor' => 'nullable|string|max:255',
                'image_url' => 'nullable|url',
                'difficulty_level' => 'in:principiante,intermedio,avanzado',
                'materials_included' => 'boolean',
                'requirements' => 'nullable|string',
                'status' => 'in:active,inactive'
            ], [
                'title.required' => 'El título del taller es obligatorio',
                'date.after_or_equal' => 'La fecha debe ser hoy o posterior',
                'time.date_format' => 'La hora debe tener formato HH:MM',
                'duration.min' => 'La duración mínima es 30 minutos',
                'duration.max' => 'La duración máxima es 8 horas (480 minutos)',
                'max_participants.min' => 'Debe haber al menos 1 participante',
                'max_participants.max' => 'Máximo 50 participantes por taller'
            ]);

            // Valores por defecto
            $validated['difficulty_level'] = $validated['difficulty_level'] ?? 'principiante';
            $validated['materials_included'] = $validated['materials_included'] ?? false;
            $validated['status'] = $validated['status'] ?? 'active';
            $validated['current_participants'] = 0;

            // Validar que no haya conflicto de horarios
            $conflictExists = Workshop::where('date', $validated['date'])
                ->where('location', $validated['location'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($validated) {
                    $startTime = Carbon::parse($validated['time']);
                    $endTime = $startTime->copy()->addMinutes($validated['duration']);

                    $q->whereBetween('time', [$startTime->format('H:i'), $endTime->format('H:i')]);
                })
                ->exists();

            if ($conflictExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un taller en esa fecha, hora y ubicación',
                    'error' => 'Conflicto de horario'
                ], 409);
            }

            // Crear taller con transacción
            \DB::beginTransaction();

            $workshop = Workshop::create($validated);

            \DB::commit();

            // Agregar campos calculados
            $workshop->available_spots = $workshop->availableSpots();
            $workshop->status_display = $workshop->status_display;
            $workshop->is_upcoming = $workshop->isUpcoming();

            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Taller creado exitosamente',
                'data' => $workshop,
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
            \Log::error('Error en WorkshopController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear taller'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Obtener taller específico",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Taller obtenido exitosamente")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($id);

            // Agregar campos calculados
            $workshop->available_spots = $workshop->availableSpots();
            $workshop->status_display = $workshop->status_display;
            $workshop->is_upcoming = $workshop->isUpcoming();

            return response()->json([
                'success' => true,
                'data' => $workshop
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Actualizar taller",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Taller actualizado exitosamente")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:30|max:480',
                'price' => 'required|numeric|min:0',
                'max_participants' => 'required|integer|min:1|max:50',
                'current_participants' => 'integer|min:0',
                'location' => 'required|string|max:255',
                'instructor' => 'nullable|string|max:255',
                'image_url' => 'nullable|url',
                'difficulty_level' => 'in:principiante,intermedio,avanzado',
                'materials_included' => 'boolean',
                'requirements' => 'nullable|string',
                'status' => 'in:active,inactive,full,cancelled'
            ]);

            // Validar que current_participants no exceda max_participants
            if (isset($validated['current_participants']) && isset($validated['max_participants'])) {
                if ($validated['current_participants'] > $validated['max_participants']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Los participantes actuales no pueden exceder el máximo'
                    ], 422);
                }
            }

            \DB::beginTransaction();

            $workshop->update($validated);

            \DB::commit();

            // Agregar campos calculados
            $workshop->available_spots = $workshop->availableSpots();
            $workshop->status_display = $workshop->status_display;
            $workshop->is_upcoming = $workshop->isUpcoming();

            return response()->json([
                'success' => true,
                'message' => 'Taller actualizado exitosamente',
                'data' => $workshop
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
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
            \Log::error('Error en WorkshopController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar taller'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Eliminar/Cancelar taller",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Taller eliminado exitosamente")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($id);

            // Si tiene participantes, cancelar en lugar de eliminar
            if ($workshop->current_participants > 0) {
                \DB::beginTransaction();
                $workshop->update(['status' => 'cancelled']);
                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Taller cancelado (tenía participantes inscritos)',
                    'action' => 'cancelled'
                ]);
            }

            // Si no tiene participantes, eliminar completamente
            \DB::beginTransaction();
            $workshop->delete();
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Taller eliminado exitosamente',
                'action' => 'deleted'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Taller no encontrado'
            ], 404);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error en WorkshopController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar taller'
            ], 500);
        }
    }
}