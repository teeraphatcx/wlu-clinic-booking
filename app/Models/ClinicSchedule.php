<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ClinicSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_open',
        'max_bookings_per_slot',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_open' => 'boolean',
            'max_bookings_per_slot' => 'integer',
        ];
    }

    public const DAYS_TH = [
        0 => 'วันอาทิตย์',
        1 => 'วันจันทร์',
        2 => 'วันอังคาร',
        3 => 'วันพุธ',
        4 => 'วันพฤหัสบดี',
        5 => 'วันศุกร์',
        6 => 'วันเสาร์',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('clinic.schedules.all.v2');
            Cache::forget('clinic.open_hours_summary');
        });
        static::deleted(function (): void {
            Cache::forget('clinic.schedules.all.v2');
            Cache::forget('clinic.open_hours_summary');
        });
    }

    public static function allCached(): Collection
    {
        $attributes = Cache::remember('clinic.schedules.all.v2', 3600, fn (): array => self::query()
            ->orderBy('day_of_week')
            ->get()
            ->map(fn (self $schedule): array => $schedule->getAttributes())
            ->all());

        return self::hydrate($attributes);
    }

    public static function openHoursSummary(): string
    {
        return Cache::remember('clinic.open_hours_summary', 3600, fn (): string => self::allCached()
            ->where('is_open', true)
            ->map(fn (self $schedule): string => str_replace('วัน', '', $schedule->dayNameTh).' '.substr($schedule->open_time, 0, 5).'-'.substr($schedule->close_time, 0, 5))
            ->implode(' • '));
    }

    /**
     * Get Thai day name.
     */
    protected function dayNameTh(): Attribute
    {
        return Attribute::make(
            get: fn () => self::DAYS_TH[$this->day_of_week] ?? '',
        );
    }

    /**
     * Get bookings on this clinic schedule day.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
