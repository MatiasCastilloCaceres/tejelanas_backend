<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'instructor',
        'image_url',
        'difficulty_level',
        'materials_included',
        'requirements',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'duration' => 'integer',
        'materials_included' => 'boolean'
    ];

    // Scopes optimizados
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::today());
    }

    public function scopeAvailable($query)
    {
        return $query->whereColumn('current_participants', '<', 'max_participants')
            ->where('status', 'active');
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    // Métodos de negocio
    public function isFull(): bool
    {
        return $this->current_participants >= $this->max_participants;
    }

    public function isUpcoming(): bool
    {
        return $this->date >= Carbon::today();
    }

    public function availableSpots(): int
    {
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function getStatusDisplayAttribute(): string
    {
        if ($this->status === 'cancelled')
            return 'Cancelado';
        if ($this->isFull())
            return 'Completo';
        if (!$this->isUpcoming())
            return 'Finalizado';
        return 'Disponible';
    }

    // Relaciones (si necesitas inscripciones futuras)
    public function registrations()
    {
        return $this->hasMany(WorkshopRegistration::class);
    }
}
