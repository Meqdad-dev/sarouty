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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // gratuit, starter, pro, agence
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_days')->nullable(); // null for free
            $table->integer('priority_level')->default(0);
            $table->integer('listing_duration_days');
            $table->integer('sponsored_listing_duration_days')->default(0);
            $table->boolean('can_create_sponsored_listing')->default(false);
            $table->integer('max_images_particulier')->default(5);
            $table->integer('max_images_agent')->default(10);
            $table->integer('max_ads_particulier')->default(2);
            $table->integer('max_ads_agent')->default(5);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
