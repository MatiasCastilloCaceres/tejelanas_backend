<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckModels extends Command
{
    protected $signature = 'check:models';
    protected $description = 'Verificar que todos los modelos existan';

    public function handle()
    {
        $models = [
            'App\Models\Product',
            'App\Models\Category',
            'App\Models\Workshop'
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $this->info("✓ {$model} existe");
            } else {
                $this->error("✗ {$model} no existe");
            }
        }

        return 0;
    }
}