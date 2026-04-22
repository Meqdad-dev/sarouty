<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée les tables auxiliaires : images, favoris, signalements.
     */
    public function up(): void
    {
        // Table des images d'annonces
        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->text('path');
            $table->string('alt')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        // Table des favoris
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Un utilisateur ne peut mettre en favori qu'une seule fois la même annonce
            $table->unique(['user_id', 'listing_id']);
        });

        // Table des signalements
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reporter_email')->nullable();
            $table->enum('reason', [
                'frauduleux',
                'doublon',
                'prix_incorrect',
                'images_incorrectes',
                'contenu_inapproprie',
                'autre'
            ]);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');
            $table->timestamps();
        });

        // Table des messages de contact
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('sender_phone')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('listing_images');
    }
};
