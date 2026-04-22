<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL : MODIFY COLUMN pour changer ENUM → VARCHAR
            DB::statement('ALTER TABLE users MODIFY COLUMN plan VARCHAR(255) NOT NULL DEFAULT "gratuit"');
            DB::statement('ALTER TABLE payments MODIFY COLUMN plan VARCHAR(255) NOT NULL DEFAULT "gratuit"');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL : ALTER COLUMN TYPE (équivalent de MODIFY COLUMN)
            DB::statement("ALTER TABLE users ALTER COLUMN plan TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE users ALTER COLUMN plan SET DEFAULT 'gratuit'");
            DB::statement("ALTER TABLE payments ALTER COLUMN plan TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE payments ALTER COLUMN plan SET DEFAULT 'gratuit'");
        }
        // SQLite : colonnes déjà en TEXT natif – rien à faire
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN plan ENUM('gratuit', 'starter', 'pro', 'agence') NOT NULL DEFAULT 'gratuit'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN plan ENUM('gratuit', 'starter', 'pro', 'agence') NOT NULL DEFAULT 'gratuit'");
        }
        // PostgreSQL : pas de ENUM natif identique — on laisse en VARCHAR
    }
};
