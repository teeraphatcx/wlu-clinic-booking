<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'is_working',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_working' => 'boolean',
        ];
    }

    /**
     * Get the therapist.
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}
