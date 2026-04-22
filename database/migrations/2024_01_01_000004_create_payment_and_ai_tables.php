<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Paiements et abonnements ─────────────────────────────────────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_session_id')->unique()->nullable();
            $table->string('stripe_payment_intent')->nullable();
            $table->enum('plan', ['gratuit', 'starter', 'pro', 'agence'])->default('gratuit');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('MAD');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // ─── Ajout des colonnes subscription aux users ─────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->enum('plan', ['gratuit', 'starter', 'pro', 'agence'])->default('gratuit')->after('role');
            $table->timestamp('plan_expires_at')->nullable()->after('plan');
            $table->unsignedSmallInteger('listings_quota')->default(2)->after('plan_expires_at');
        });

        // ─── Modération IA ─────────────────────────────────────────────────────
        Schema::create('ai_moderations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->boolean('approved')->default(true);
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->json('flags')->nullable();
            $table->text('reason')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->timestamps();
        });

        // ─── Alertes de recherche (newsletter annonces) ────────────────────────
        Schema::create('search_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('property_type')->nullable();
            $table->decimal('price_min', 12, 2)->nullable();
            $table->decimal('price_max', 12, 2)->nullable();
            $table->unsignedSmallInteger('surface_min')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });

        // ─── Statistiques annonces (vues par jour) ─────────────────────────────
        Schema::create('listing_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('contacts')->default(0);
            $table->unsignedInteger('favorites')->default(0);
            $table->timestamps();

            $table->unique(['listing_id', 'date']);
        });

        // ─── Notifications in-app ──────────────────────────────────────────────
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // listing_approved, new_message, etc.
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('listing_stats');
        Schema::dropIfExists('search_alerts');
        Schema::dropIfExists('ai_moderations');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan', 'plan_expires_at', 'listings_quota']);
        });
        Schema::dropIfExists('payments');
    }
};
