<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_number',
        'start_time',
        'end_time',
        'label_th',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'slot_number' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get bookings in this time slot.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope active time slots.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('slot_number');
    }
}
