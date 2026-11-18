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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('hn')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->date('birthdate')->nullable();
            $table->text('address')->nullable();
            $table->timestamp('pdpa_accepted_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
