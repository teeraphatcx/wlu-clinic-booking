<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistDayOff extends Model
{
    use HasFactory;

    protected $table = 'therapist_days_off';

    protected $fillable = [
        'therapist_id',
        'date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
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
