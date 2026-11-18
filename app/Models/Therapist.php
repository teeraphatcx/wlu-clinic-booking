<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Therapist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'nickname',
        'phone',
        'gender',
        'photo',
        'bio',
        'start_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get therapist's full name.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /**
     * Get therapist's display name with nickname.
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $name = "{$this->first_name} {$this->last_name}";

                return $this->nickname ? "{$name} ({$this->nickname})" : $name;
            },
        );
    }

    /**
     * Get bookings assigned to this therapist.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get services this therapist can perform.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'therapist_services')->withTimestamps();
    }

    /**
     * Get weekly schedule of this therapist.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TherapistSchedule::class);
    }

    /**
     * Get specific days off of this therapist.
     */
    public function daysOff(): HasMany
    {
        return $this->hasMany(TherapistDayOff::class);
    }

    /**
     * Get blocked periods for this therapist.
     */
    public function blockedPeriods(): HasMany
    {
        return $this->hasMany(TherapistBlockedPeriod::class);
    }

    /**
     * Scope active therapists.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
