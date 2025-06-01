<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el request tiene el header de autorización
        if (!$request->hasHeader('Authorization')) {
            return response()->json([
                'error' => 'Sin autorización'
            ], 401);
        }

        $authHeader = $request->header('Authorization');

        // Verificar formato Bearer token
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'error' => 'Formato de token inválido'
            ], 401);
        }

        // Extraer el token
        $token = substr($authHeader, 7);

        // Aquí puedes agregar tu lógica de validación de token
        // Por ahora, validamos que no esté vacío
        if (empty($token)) {
            return response()->json([
                'error' => 'Token vacío'
            ], 401);
        }

        // Token válido simple para desarrollo
        $validTokens = [
            'tejelanas_admin_token_2025',
            'development_token_123',
            'test_token_456'
        ];

        if (!in_array($token, $validTokens)) {
            return response()->json([
                'error' => 'Token inválido'
            ], 401);
        }

        return $next($request);
    }
}