<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Workshop;
use App\Models\AboutUs;
use App\Models\Faq;

class TejelanasSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías
        $categories = [
            [
                'name' => 'Lanas Premium',
                'description' => 'Lanas de alta calidad importadas',
                'status' => 'active'
            ],
            [
                'name' => 'Hilos de Algodón',
                'description' => 'Hilos 100% algodón natural',
                'status' => 'active'
            ],
            [
                'name' => 'Accesorios',
                'description' => 'Agujas, patrones y herramientas',
                'status' => 'active'
            ],
            [
                'name' => 'Productos Terminados',
                'description' => 'Prendas y artículos listos',
                'status' => 'active'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Crear productos
        $products = [
            [
                'name' => 'Lana Merino Azul',
                'description' => 'Lana 100% merino, perfecta para prendas delicadas',
                'price' => 15990,
                'stock' => 25,
                'category_id' => 1,
                'color' => 'Azul',
                'material' => '100% Merino',
                'status' => 'active'
            ],
            [
                'name' => 'Hilo Algodón Blanco',
                'description' => 'Hilo de algodón egipcio, ideal para prendas de verano',
                'price' => 8990,
                'stock' => 40,
                'category_id' => 2,
                'color' => 'Blanco',
                'material' => '100% Algodón',
                'status' => 'active'
            ],
            [
                'name' => 'Agujas Bambú Set',
                'description' => 'Set de agujas de bambú, números 3-10',
                'price' => 25990,
                'stock' => 15,
                'category_id' => 3,
                'material' => 'Bambú',
                'status' => 'active'
            ],
            [
                'name' => 'Chaleco Tejido a Mano',
                'description' => 'Hermoso chaleco tejido en lana alpaca',
                'price' => 45990,
                'stock' => 5,
                'category_id' => 4,
                'color' => 'Beige',
                'material' => '100% Alpaca',
                'status' => 'active'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Crear talleres
        Workshop::create([
            'title' => 'Taller de Tejido Básico',
            'description' => 'Aprende las técnicas fundamentales del tejido a dos agujas. Perfecto para principiantes.',
            'date' => now()->addDays(15)->format('Y-m-d'),
            'time' => '14:00:00',
            'duration' => 180,
            'price' => 25000,
            'max_participants' => 8,
            'current_participants' => 3,
            'location' => 'Taller Tejelanas Vivi - Sala Principal',
            'instructor' => 'Viviana González',
            'difficulty_level' => 'principiante',
            'materials_included' => true,
            'requirements' => 'No se requiere experiencia previa',
            'status' => 'active'
        ]);

        Workshop::create([
            'title' => 'Técnicas Avanzadas de Crochet',
            'description' => 'Domina las técnicas más complejas del crochet y crea piezas únicas.',
            'date' => now()->addDays(25)->format('Y-m-d'),
            'time' => '10:00:00',
            'duration' => 240,
            'price' => 35000,
            'max_participants' => 6,
            'current_participants' => 1,
            'location' => 'Taller Tejelanas Vivi - Sala Avanzada',
            'instructor' => 'Carmen Silva',
            'difficulty_level' => 'avanzado',
            'materials_included' => true,
            'requirements' => 'Conocimientos básicos de crochet',
            'status' => 'active'
        ]);

        // Crear contenido About Us
        $aboutSections = [
            [
                'title' => 'Nuestra Historia',
                'content' => 'Tejelanas Vivi nació del amor por las fibras naturales y la tradición del tejido. Desde 2015, nos dedicamos a promover este hermoso arte, conectando a personas con la creatividad y la paciencia que requiere crear con las manos.',
                'section' => 'historia',
                'order' => 1,
                'status' => 'active'
            ],
            [
                'title' => 'Nuestra Misión',
                'content' => 'Preservar y enseñar las técnicas tradicionales del tejido, mientras innovamos con nuevos diseños y materiales sostenibles. Creemos en el poder terapéutico y creativo del tejido.',
                'section' => 'mision',
                'order' => 2,
                'status' => 'active'
            ],
            [
                'title' => 'Nuestra Visión',
                'content' => 'Ser la comunidad de tejido más reconocida de Chile, donde cada persona pueda desarrollar su creatividad y encontrar su pasión por las manualidades.',
                'section' => 'vision',
                'order' => 3,
                'status' => 'active'
            ]
        ];

        foreach ($aboutSections as $section) {
            AboutUs::create($section);
        }

        // Crear FAQs
        $faqs = [
            [
                'question' => '¿Cuál es el tiempo de entrega de los productos?',
                'answer' => 'Los tiempos de entrega varían entre 3-5 días hábiles en Santiago y 5-7 días hábiles en regiones. Los productos personalizados pueden tomar hasta 10 días.',
                'category' => 'envios',
                'order' => 1,
                'featured' => true,
                'status' => 'active'
            ],
            [
                'question' => '¿Los talleres incluyen todos los materiales?',
                'answer' => 'Sí, todos nuestros talleres incluyen los materiales necesarios para completar el proyecto del día. Solo necesitas traer ganas de aprender.',
                'category' => 'talleres',
                'order' => 2,
                'featured' => true,
                'status' => 'active'
            ],
            [
                'question' => '¿Hacen productos personalizados?',
                'answer' => 'Por supuesto. Ofrecemos servicios de tejido personalizado. Contáctanos para discutir tu proyecto específico y recibir una cotización.',
                'category' => 'productos',
                'order' => 3,
                'featured' => false,
                'status' => 'active'
            ],
            [
                'question' => '¿Cuáles son los métodos de pago aceptados?',
                'answer' => 'Aceptamos transferencias bancarias, tarjetas de débito y crédito, y pagos en efectivo en nuestro taller físico.',
                'category' => 'general',
                'order' => 4,
                'featured' => false,
                'status' => 'active'
            ]
        ];

        foreach ($faqs as $faqData) {
            Faq::create($faqData);
        }

        echo "✅ Base de datos poblada exitosamente con datos de Tejelanas Vivi\n";
    }
}