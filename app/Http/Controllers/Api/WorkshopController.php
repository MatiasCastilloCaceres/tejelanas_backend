<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="Workshop",
 *     type="object",
 *     title="Workshop",
 *     description="Taller de crochet de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Taller de Crochet Básico"),
 *     @OA\Property(property="description", type="string", example="Aprende los fundamentos del crochet"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-06-15"),
 *     @OA\Property(property="time", type="string", format="time", example="14:00:00"),
 *     @OA\Property(property="duration", type="integer", example=120),
 *     @OA\Property(property="price", type="number", format="float", example=25000.00),
 *     @OA\Property(property="max_participants", type="integer", example=8),
 *     @OA\Property(property="current_participants", type="integer", example=3),
 *     @OA\Property(property="location", type="string", example="TEJElANAS, Laguna de Zapallar"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "full", "cancelled"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="Workshops",
 *     description="Operaciones CRUD para talleres de crochet"
 * )
 */
class WorkshopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/workshops",
     *     tags={"Workshops"},
     *     summary="Obtener lista de talleres",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de talleres",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Workshop")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $workshops = Workshop::orderBy('date', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $workshops
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/workshops",
     *     tags={"Workshops"},
     *     summary="Crear nuevo taller",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Crochet Básico"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-07-15"),
     *             @OA\Property(property="time", type="string", example="14:00"),
     *             @OA\Property(property="duration", type="integer", example=120),
     *             @OA\Property(property="price", type="number", example=25000.00),
     *             @OA\Property(property="max_participants", type="integer", example=8)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Taller creado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:30',
            'price' => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1',
            'location' => 'nullable|string',
            'status' => 'in:active,inactive,full,cancelled'
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['current_participants'] = 0;
        $validated['location'] = $validated['location'] ?? 'TEJElANAS, Laguna de Zapallar';

        $workshop = Workshop::create($validated);

        return response()->json([
            'success' => true,
            'data' => $workshop
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Obtener taller específico",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Taller obtenido")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $workshop
            ]);
        } catch (\Exception $e) {
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
     *     @OA\Response(response=200, description="Taller actualizado")
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
                'duration' => 'required|integer|min:30',
                'price' => 'required|numeric|min:0',
                'max_participants' => 'required|integer|min:1',
                'status' => 'in:active,inactive,full,cancelled'
            ]);

            $workshop->update($validated);

            return response()->json([
                'success' => true,
                'data' => $workshop
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
     *     path="/api/v1/workshops/{id}",
     *     tags={"Workshops"},
     *     summary="Eliminar taller",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Taller eliminado")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($id);
            $workshop->delete();

            return response()->json([
                'success' => true,
                'message' => 'Taller eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar'
            ], 500);
        }
    }
}