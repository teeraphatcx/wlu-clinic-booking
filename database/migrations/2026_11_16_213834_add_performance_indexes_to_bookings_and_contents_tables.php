<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->index(
                ['booking_date', 'time_slot_id', 'therapist_id', 'status'],
                'bookings_availability_lookup_idx',
            );
        });

        Schema::table('contents', function (Blueprint $table): void {
            $table->index(
                ['is_active', 'type', 'published_at', 'expired_at', 'sort_order'],
                'contents_published_lookup_idx',
            );
        });

        /*
         * Recommendation for a later review: therapist_schedules currently has
         * both a unique and a non-unique index on (therapist_id, day_of_week).
         * They are intentionally left unchanged in this migration.
         */
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->dropIndex('contents_published_lookup_idx');
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropIndex('bookings_availability_lookup_idx');
        });
    }
};
