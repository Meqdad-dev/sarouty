<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes de sponsorisation aux annonces
     * et la limite d'images par annonce aux utilisateurs.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_sponsored')->default(false)->after('priority');
            $table->timestamp('sponsored_until')->nullable()->after('is_sponsored');
            $table->index(['is_sponsored', 'sponsored_until']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_images_per_ad')->default(5)->after('listings_quota');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['is_sponsored', 'sponsored_until']);
            $table->dropColumn(['is_sponsored', 'sponsored_until']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_images_per_ad');
        });
    }
};
