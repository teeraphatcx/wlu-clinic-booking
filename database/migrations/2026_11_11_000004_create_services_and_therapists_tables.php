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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_th');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->boolean('requires_therapist_selection')->default(false);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nickname')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('female');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });

        Schema::create('therapist_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['therapist_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_services');
        Schema::dropIfExists('therapists');
        Schema::dropIfExists('services');
    }
};
