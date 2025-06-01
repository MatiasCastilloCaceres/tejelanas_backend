<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'featured',
        'status',
        'views_count'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views_count' => 'integer'
    ];

    // Scopes para consultas optimizadas
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Incrementar contador de vistas
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Obtener categorías disponibles
    public static function getCategories()
    {
        return ['general', 'productos', 'talleres', 'envios'];
    }
}
