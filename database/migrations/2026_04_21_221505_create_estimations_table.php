<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimations', function (Blueprint $table) {
            $table->id();

            // Bien
            $table->string('property_type');          // appartement, villa, terrain...
            $table->string('transaction_type');       // vente, location, neuf, vacances
            $table->string('city');
            $table->decimal('surface', 8, 2)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->smallInteger('floor')->nullable();
            $table->year('construction_year')->nullable();
            $table->string('condition')->nullable();  // neuf, excellent, bon, a_renover

            // Équipements
            $table->boolean('has_garage')->default(false);
            $table->unsignedTinyInteger('garage_places')->nullable();
            $table->boolean('has_garden')->default(false);
            $table->decimal('garden_surface', 7, 2)->nullable();
            $table->boolean('has_terrace')->default(false);
            $table->decimal('terrace_surface', 7, 2)->nullable();
            $table->boolean('has_pool')->default(false);
            $table->boolean('has_elevator')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('is_furnished')->default(false);
            $table->boolean('has_security')->default(false);

            // Résultats calculés
            $table->decimal('estimated_min', 14, 2)->nullable();
            $table->decimal('estimated_mid', 14, 2)->nullable();
            $table->decimal('estimated_max', 14, 2)->nullable();
            $table->decimal('price_per_sqm', 10, 2)->nullable();

            // Profil utilisateur
            $table->string('user_type')->nullable();           // proprietaire, acheteur, locataire, agent, investisseur
            $table->boolean('wants_professional_help')->default(false);
            $table->boolean('is_owner')->default(false);
            $table->string('timeline')->nullable();            // maintenant, 3mois, 6mois, plus
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            // Méta
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimations');
    }
};
