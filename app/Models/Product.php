<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Producto de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lana Natural Merino"),
 *     @OA\Property(property="description", type="string", example="Lana natural de alta calidad"),
 *     @OA\Property(property="price", type="number", format="float", example=15990.50),
 *     @OA\Property(property="stock", type="integer", example=25),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="weight", type="number", format="float", example=100.0),
 *     @OA\Property(property="color", type="string", example="Azul marino"),
 *     @OA\Property(property="material", type="string", example="100% Lana Merino"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image_url',
        'weight',
        'color',
        'material',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2'
    ];

    // Relación con categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scope para productos activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope para productos con stock
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}