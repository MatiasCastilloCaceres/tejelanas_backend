<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lanas Naturales',
                'description' => 'Lanas 100% naturales de oveja, alpaca y vicuña',
                'status' => 'active'
            ],
            [
                'name' => 'Lanas Sintéticas',
                'description' => 'Lanas acrílicas y mezclas sintéticas de alta calidad',
                'status' => 'active'
            ],
            [
                'name' => 'Vellón',
                'description' => 'Vellón natural sin procesar para hilar',
                'status' => 'active'
            ],
            [
                'name' => 'Herramientas',
                'description' => 'Agujas, ganchillos y herramientas para tejido',
                'status' => 'active'
            ],
            [
                'name' => 'Accesorios',
                'description' => 'Botones, cierres y complementos para tejidos',
                'status' => 'active'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}