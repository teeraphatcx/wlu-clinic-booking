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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->enum('group', ['general', 'booking', 'notification', 'website'])->default('general');
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('group');
            $table->index('is_public');
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['news', 'announcement', 'banner', 'faq'])->default('news');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
        Schema::dropIfExists('settings');
    }
};
