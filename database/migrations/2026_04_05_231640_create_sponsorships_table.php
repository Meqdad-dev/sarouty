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
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            
            // Sponsorship details
            $table->enum('type', ['basic', 'premium', 'premium_plus'])->default('basic');
            $table->enum('status', ['pending', 'active', 'paused', 'expired', 'cancelled'])->default('pending');
            
            // Duration
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Pricing
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('MAD');
            $table->unsignedSmallInteger('duration_days')->default(7);
            
            // Performance tracking
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('contacts')->default(0);
            
            // Admin notes
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['listing_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
