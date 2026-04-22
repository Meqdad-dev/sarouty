<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute 'client' aux valeurs autorisées du champ 'role'.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent', 'particulier', 'client') NOT NULL DEFAULT 'particulier'");
        }
        // SQLite ne supporte pas MODIFY COLUMN — on ignore
        // (SQLite est uniquement utilisé en local/test, MySQL en production)
    }

    /**
     * Retire 'client' des valeurs autorisées (rollback).
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::table('users')->where('role', 'client')->update(['role' => 'particulier']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent', 'particulier') NOT NULL DEFAULT 'particulier'");
        }
    }
};
