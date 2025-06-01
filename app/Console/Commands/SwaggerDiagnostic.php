<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ReflectionClass;

class SwaggerDiagnostic extends Command
{
    protected $signature = 'swagger:diagnostic';
    protected $description = 'Diagnosticar problemas con Swagger';

    public function handle()
    {
        $this->info('=== Diagnóstico Swagger ===');

        // Verificar modelos
        $models = [
            'App\Models\Product',
            'App\Models\Category',
            'App\Models\Workshop'
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $reflection = new ReflectionClass($model);
                $this->info("✓ {$model} existe en: " . $reflection->getFileName());
            } else {
                $this->error("✗ {$model} no existe");
            }
        }

        // Verificar controladores
        $controllers = [
            'App\Http\Controllers\Api\ProductController',
            'App\Http\Controllers\Api\CategoryController',
            'App\Http\Controllers\Api\WorkshopController'
        ];

        foreach ($controllers as $controller) {
            if (class_exists($controller)) {
                $this->info("✓ {$controller} existe");
            } else {
                $this->error("✗ {$controller} no existe");
            }
        }

        // Verificar directorios
        $directories = [
            storage_path('api-docs'),
            base_path('app/Models'),
            base_path('app/Http/Controllers/Api')
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $this->info("✓ Directorio existe: {$dir}");
            } else {
                $this->error("✗ Directorio no existe: {$dir}");
            }
        }

        return 0;
    }
}