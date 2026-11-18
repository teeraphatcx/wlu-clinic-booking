<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistBlockedPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'start_datetime',
        'end_datetime',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
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
