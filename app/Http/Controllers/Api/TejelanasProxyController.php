<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(
 *      title="API Proxy Tejelanas Vivi",
 *      version="1.0.0",
 *      description="API proxy para consultar información de Tejelanas Vivi con CORS habilitado"
 * )
 * 
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Servidor proxy local"
 * )
 */
class TejelanasProxyController extends Controller
{
    private $baseUrl = 'https://www.clinicatecnologica.cl/ipss/tejelanasVivi/api/v1';
    private $bearerToken = 'ipss.get';

    /**
     * @OA\Get(
     *      path="/api/products-services",
     *      operationId="getProductsServices",
     *      tags={"Tejelanas Proxy"},
     *      summary="Obtener productos y servicios",
     *      description="Consulta productos y servicios desde API externa",
     *      @OA\Response(
     *          response=200,
     *          description="Datos obtenidos exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object"),
     *              @OA\Property(property="source", type="string", example="external_api")
     *          )
     *      )
     * )
     */
    public function getProductsServices(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Accept' => 'application/json',
            ])
                ->withOptions([
                    'verify' => false, // Deshabilita verificación SSL para desarrollo
                ])
                ->timeout(30)
                ->get($this->baseUrl . '/products-services/');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'source' => 'external_api',
                    'endpoint' => $this->baseUrl . '/products-services/'
                ])->header('Access-Control-Allow-Origin', '*');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al consultar API externa',
                    'status_code' => $response->status()
                ], $response->status())->header('Access-Control-Allow-Origin', '*');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión',
                'error' => $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * @OA\Get(
     *      path="/api/about-us",
     *      operationId="getAboutUs",
     *      tags={"Tejelanas Proxy"},
     *      summary="Obtener información sobre nosotros",
     *      description="Consulta información sobre Tejelanas Vivi desde API externa",
     *      @OA\Response(
     *          response=200,
     *          description="Información obtenida exitosamente"
     *      )
     * )
     */
    public function getAboutUs()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Accept' => 'application/json',
            ])
                ->withOptions([
                    'verify' => false,
                ])
                ->timeout(30)
                ->get($this->baseUrl . '/about-us/');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'source' => 'external_api',
                    'endpoint' => $this->baseUrl . '/about-us/'
                ])->header('Access-Control-Allow-Origin', '*');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al consultar API externa',
                    'status_code' => $response->status()
                ], $response->status())->header('Access-Control-Allow-Origin', '*');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión',
                'error' => $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * @OA\Get(
     *      path="/api/faq",
     *      operationId="getFaq",
     *      tags={"Tejelanas Proxy"},
     *      summary="Obtener FAQ",
     *      description="Consulta preguntas frecuentes desde API externa",
     *      @OA\Response(
     *          response=200,
     *          description="FAQ obtenido exitosamente"
     *      )
     * )
     */
    public function getFaq(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'Accept' => 'application/json',
            ])
                ->withOptions([
                    'verify' => false,
                ])
                ->timeout(30)
                ->get($this->baseUrl . '/faq/');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                    'source' => 'external_api',
                    'endpoint' => $this->baseUrl . '/faq/'
                ])->header('Access-Control-Allow-Origin', '*');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al consultar API externa',
                    'status_code' => $response->status()
                ], $response->status())->header('Access-Control-Allow-Origin', '*');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión',
                'error' => $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }
}