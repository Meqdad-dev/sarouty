<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('theme_color', 30)->default('gray')->after('features');
        });

        DB::table('subscription_plans')->where('slug', 'gratuit')->update(['theme_color' => 'gray']);
        DB::table('subscription_plans')->where('slug', 'starter')->update(['theme_color' => 'gold']);
        DB::table('subscription_plans')->where('slug', 'pro')->update(['theme_color' => 'ink']);
        DB::table('subscription_plans')->where('slug', 'agence')->update(['theme_color' => 'terracotta']);
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('theme_color');
        });
    }
};
