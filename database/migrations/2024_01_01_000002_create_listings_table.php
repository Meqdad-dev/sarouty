<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table listings (annonces immobilières).
     */
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Informations principales
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->decimal('surface', 8, 2)->nullable(); // en m²
            $table->unsignedTinyInteger('rooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('floor')->nullable();

            // Type et catégorie
            $table->enum('transaction_type', [
                'vente',
                'location',
                'neuf',
                'vacances'
            ])->default('vente');

            $table->enum('property_type', [
                'appartement',
                'villa',
                'terrain',
                'riad',
                'bureau',
                'commerce',
                'maison',
                'ferme'
            ])->default('appartement');

            // Localisation
            $table->string('city');
            $table->string('zone')->nullable(); // quartier
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Caractéristiques
            $table->boolean('furnished')->default(false);
            $table->boolean('parking')->default(false);
            $table->boolean('elevator')->default(false);
            $table->boolean('pool')->default(false);
            $table->boolean('garden')->default(false);
            $table->boolean('terrace')->default(false);
            $table->boolean('security')->default(false);

            // Statut et modération
            $table->enum('status', ['pending', 'active', 'rejected', 'sold', 'rented'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('featured')->default(false);

            // Image principale (thumbnail)
            $table->text('thumbnail')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index pour les recherches
            $table->index(['city', 'status']);
            $table->index(['transaction_type', 'property_type', 'status']);
            $table->index(['price', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
