<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Workshop",
 *     type="object",
 *     title="Workshop",
 *     description="Taller de crochet de Tejelanas Vivi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Taller de Crochet Básico"),
 *     @OA\Property(property="description", type="string", example="Aprende los fundamentos del crochet"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-06-15"),
 *     @OA\Property(property="time", type="string", format="time", example="14:00:00"),
 *     @OA\Property(property="duration", type="integer", example=120, description="Duración en minutos"),
 *     @OA\Property(property="price", type="number", format="float", example=25000.00),
 *     @OA\Property(property="max_participants", type="integer", example=8),
 *     @OA\Property(property="current_participants", type="integer", example=3),
 *     @OA\Property(property="location", type="string", example="TEJElANAS, Laguna de Zapallar"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "full", "cancelled"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'duration',
        'price',
        'max_participants',
        'current_participants',
        'location',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'price' => 'decimal:2',
        'duration' => 'integer'
    ];
}