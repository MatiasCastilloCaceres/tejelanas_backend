<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     * Implementa caché para requests GET
     */
    public function handle(Request $request, Closure $next, int $minutes = 5): Response
    {
        // Solo cachear requests GET
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Crear clave única del cache basada en URL y parámetros
        $cacheKey = 'api_cache:' . md5($request->fullUrl());

        // Verificar si existe en cache
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);

            $response = response($cachedResponse['content'], $cachedResponse['status']);
            $response->headers->replace($cachedResponse['headers']);
            $response->headers->set('X-Cache', 'HIT');
            $response->headers->set('X-Cache-Key', $cacheKey);

            return $response;
        }

        // Procesar request
        $response = $next($request);

        // Solo cachear respuestas exitosas
        if ($response->getStatusCode() === 200) {
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all()
            ];

            Cache::put($cacheKey, $cacheData, now()->addMinutes($minutes));
            $response->headers->set('X-Cache', 'MISS');
            $response->headers->set('X-Cache-TTL', $minutes * 60);
        }

        $response->headers->set('X-Cache-Key', $cacheKey);
        return $response;
    }
}