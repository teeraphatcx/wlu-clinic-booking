<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_NO_SHOW = 'no_show';

    public const STATUSES = [
        self::STATUS_PENDING => 'รอยืนยัน',
        self::STATUS_CONFIRMED => 'ยืนยันแล้ว',
        self::STATUS_COMPLETED => 'เสร็จสิ้น',
        self::STATUS_CANCELLED => 'ยกเลิก',
        self::STATUS_NO_SHOW => 'ไม่มาตามนัด',
    ];

    protected $fillable = [
        'booking_no',
        'customer_id',
        'service_id',
        'therapist_id',
        'clinic_schedule_id',
        'time_slot_id',
        'booking_date',
        'status',
        'notes',
        'admin_notes',
        'cancelled_at',
        'cancel_reason',
        'pdpa_accepted',
        'ip_address',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'pdpa_accepted' => 'boolean',
            'cancelled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    /**
     * Customer who made the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Service booked.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Assigned therapist (optional).
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    /**
     * Clinic schedule rule for the booking.
     */
    public function clinicSchedule(): BelongsTo
    {
        return $this->belongsTo(ClinicSchedule::class);
    }

    /**
     * Time slot for the booking.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Change history of the booking.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(BookingHistory::class);
    }

    /**
     * Scope bookings by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Generate unique booking number: WLU-YYMMDD-XXXX
     */
    public static function generateBookingNo($date = null): string
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        $prefix = 'WLU-'.$targetDate->format('ymd').'-';

        $latest = self::withTrashed()
            ->where('booking_no', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderBy('id', 'desc')
            ->value('booking_no');

        if ($latest) {
            $number = intval(substr($latest, -4)) + 1;
        } else {
            $number = 1;
        }

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
