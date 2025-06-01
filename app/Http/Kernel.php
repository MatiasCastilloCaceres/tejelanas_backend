<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     */
    protected $routeMiddleware = [
        // ...existing middleware...
        'custom.bearer' => \App\Http\Middleware\CustomBearerAuth::class,
    ];
}