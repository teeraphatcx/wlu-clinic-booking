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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_no')->unique(); // WLU-YYMMDD-XXXX
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('therapist_id')->nullable()->constrained('therapists')->onDelete('set null');
            $table->foreignId('clinic_schedule_id')->nullable()->constrained('clinic_schedules')->onDelete('set null');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('restrict');
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->boolean('pdpa_accepted')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_date', 'status']);
            $table->index('customer_id');
            $table->index('therapist_id');
            $table->index('time_slot_id');
        });

        Schema::create('booking_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->string('action'); // created, status_changed, rescheduled, cancelled, note_added
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_histories');
        Schema::dropIfExists('bookings');
    }
};
