<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Workshop;
use Carbon\Carbon;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $workshops = [
            [
                'title' => 'Crochet para Principiantes',
                'description' => 'Aprende los puntos básicos del crochet: cadena, punto bajo, punto alto. Incluye materiales.',
                'date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'time' => '14:00',
                'duration' => 120,
                'price' => 25000.00,
                'max_participants' => 8,
                'current_participants' => 3,
                'location' => 'TEJElANAS, Laguna de Zapallar',
                'status' => 'active'
            ],
            [
                'title' => 'Técnicas Avanzadas de Crochet',
                'description' => 'Puntos complejos, texturas y patrones avanzados. Requisito: conocimientos básicos.',
                'date' => Carbon::now()->addDays(22)->format('Y-m-d'),
                'time' => '15:30',
                'duration' => 180,
                'price' => 35000.00,
                'max_participants' => 6,
                'current_participants' => 2,
                'location' => 'TEJElANAS, Laguna de Zapallar',
                'status' => 'active'
            ],
            [
                'title' => 'Amigurumi: Muñecos en Crochet',
                'description' => 'Crea adorables muñecos y figuras en crochet. Técnicas de armado y terminaciones.',
                'date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'time' => '10:00',
                'duration' => 240,
                'price' => 42000.00,
                'max_participants' => 5,
                'current_participants' => 5,
                'location' => 'TEJElANAS, Laguna de Zapallar',
                'status' => 'full'
            ],
            [
                'title' => 'Taller de Hilar Lana',
                'description' => 'Aprende a hilar tu propia lana desde el vellón. Incluye uso de rueca.',
                'date' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'time' => '09:00',
                'duration' => 300,
                'price' => 55000.00,
                'max_participants' => 4,
                'current_participants' => 1,
                'location' => 'TEJElANAS, Laguna de Zapallar',
                'status' => 'active'
            ],
            [
                'title' => 'Mantas y Colchas Grandes',
                'description' => 'Técnicas para proyectos grandes. Unión de cuadrados y patrones continuos.',
                'date' => Carbon::now()->addDays(60)->format('Y-m-d'),
                'time' => '13:00',
                'duration' => 210,
                'price' => 38000.00,
                'max_participants' => 6,
                'current_participants' => 0,
                'location' => 'TEJElANAS, Laguna de Zapallar',
                'status' => 'active'
            ]
        ];

        foreach ($workshops as $workshop) {
            Workshop::create($workshop);
        }
    }
}