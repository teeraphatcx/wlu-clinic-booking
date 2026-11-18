<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hn',
        'first_name',
        'last_name',
        'phone',
        'email',
        'gender',
        'birthdate',
        'address',
        'pdpa_accepted_at',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'pdpa_accepted_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the customer's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /**
     * Get the bookings for the customer.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
