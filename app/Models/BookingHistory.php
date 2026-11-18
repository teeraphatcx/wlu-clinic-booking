<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'admin_id',
        'action',
        'old_status',
        'new_status',
        'old_values',
        'new_values',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    /**
     * Booking this history belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Admin who triggered the change (null if by customer/system).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
