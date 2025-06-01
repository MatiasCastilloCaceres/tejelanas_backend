<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Schema(
 *     schema="AboutUs",
 *     type="object",
 *     title="About Us",
 *     description="Información sobre Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Nuestra Historia"),
 *     @OA\Property(property="content", type="string", example="Tejelanas Vivi nació del amor por las fibras naturales..."),
 *     @OA\Property(property="image_url", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="section", type="string", enum={"historia", "mision", "vision", "valores"}, example="historia"),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="About Us",
 *     description="Información sobre la empresa Tejelanas Vivi"
 * )
 */
class AboutUsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/about-us",
     *     tags={"About Us"},
     *     summary="Obtener información sobre nosotros",
     *     description="Obtiene todas las secciones de información sobre Tejelanas Vivi",
     *     @OA\Parameter(
     *         name="section",
     *         in="query",
     *         required=false,
     *         description="Filtrar por sección específica",
     *         @OA\Schema(type="string", enum={"historia", "mision", "vision", "valores"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Información obtenida exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AboutUs")
     *             ),
     *             @OA\Property(property="response_time", type="number", format="float", example=0.015)
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'section' => 'in:historia,mision,vision,valores'
            ]);

            $query = AboutUs::where('status', 'active')
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'asc');

            if (isset($validated['section'])) {
                $query->where('section', $validated['section']);
            }

            $aboutUs = $query->get();
            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Información obtenida exitosamente',
                'data' => $aboutUs,
                'response_time' => $responseTime
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en AboutUsController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/about-us",
     *     tags={"About Us"},
     *     summary="Crear nueva sección About Us",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "section"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Nuestra Misión"),
     *             @OA\Property(property="content", type="string", example="Crear productos textiles únicos..."),
     *             @OA\Property(property="image_url", type="string", format="url", example="https://example.com/image.jpg"),
     *             @OA\Property(property="section", type="string", enum={"historia", "mision", "vision", "valores"}, example="mision"),
     *             @OA\Property(property="order", type="integer", minimum=1, example=1),
     *             @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Sección creada exitosamente")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'nullable|url',
                'section' => 'required|in:historia,mision,vision,valores',
                'order' => 'integer|min:1',
                'status' => 'in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';
            $validated['order'] = $validated['order'] ?? 1;

            $aboutUs = AboutUs::create($validated);
            $responseTime = round((microtime(true) - $startTime), 3);

            return response()->json([
                'success' => true,
                'message' => 'Sección creada exitosamente',
                'data' => $aboutUs,
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
     *     path="/api/v1/about-us/{id}",
     *     tags={"About Us"},
     *     summary="Obtener sección específica",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sección obtenida exitosamente")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $aboutUs
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sección no encontrada'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/about-us/{id}",
     *     tags={"About Us"},
     *     summary="Actualizar sección About Us",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sección actualizada exitosamente")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'nullable|url',
                'section' => 'required|in:historia,mision,vision,valores',
                'order' => 'integer|min:1',
                'status' => 'in:active,inactive'
            ]);

            $aboutUs->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sección actualizada exitosamente',
                'data' => $aboutUs
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sección no encontrada'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/about-us/{id}",
     *     tags={"About Us"},
     *     summary="Eliminar sección About Us",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Sección eliminada exitosamente")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);
            $aboutUs->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sección eliminada exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sección no encontrada'
            ], 404);
        }
    }
}
