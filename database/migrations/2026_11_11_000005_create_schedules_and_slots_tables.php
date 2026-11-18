<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinic_schedules', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->time('open_time')->default('08:00:00');
            $table->time('close_time')->default('16:30:00');
            $table->boolean('is_open')->default(true);
            $table->unsignedInteger('max_bookings_per_slot')->default(5);
            $table->timestamps();

            $table->unique('day_of_week');
        });

        Schema::create('clinic_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->boolean('is_full_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('date');
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('slot_number'); // 1-9
            $table->time('start_time');
            $table->time('end_time');
            $table->string('label_th'); // e.g. "08:00 - 09:00"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('slot_number');
            $table->index('is_active');
        });

        Schema::create('therapist_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->default('16:30:00');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->boolean('is_working')->default(true);
            $table->timestamps();

            $table->index(['therapist_id', 'day_of_week']);
            $table->unique(['therapist_id', 'day_of_week']);
        });

        Schema::create('therapist_days_off', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
            $table->date('date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['therapist_id', 'date']);
            $table->index('date');
        });

        Schema::create('therapist_blocked_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['therapist_id', 'start_datetime', 'end_datetime'], 'therapist_blocked_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_blocked_periods');
        Schema::dropIfExists('therapist_days_off');
        Schema::dropIfExists('therapist_schedules');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('clinic_holidays');
        Schema::dropIfExists('clinic_schedules');
    }
};
