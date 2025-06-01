<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomBearerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        // Verificar si el token es el esperado (ipss.get)
        if ($token !== 'ipss.get') {
            return response()->json([
                'success' => false,
                'message' => 'Token de autorización inválido',
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
