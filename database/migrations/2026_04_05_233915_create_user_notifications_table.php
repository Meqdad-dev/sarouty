<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the missing 'data' json column and extra index to user_notifications.
     * The table itself was already created in 2024_01_01_000004.
     */
    public function up(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            // Add 'data' column if it doesn't already exist
            if (!Schema::hasColumn('user_notifications', 'data')) {
                $table->json('data')->nullable()->after('read');
            }

            // Add secondary index on (user_id, created_at)
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);

            if (Schema::hasColumn('user_notifications', 'data')) {
                $table->dropColumn('data');
            }
        });
    }
};
