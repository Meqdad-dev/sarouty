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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
            if (!Schema::hasColumn('messages', 'subject')) {
                $table->string('subject')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('messages', 'replied_to_id')) {
                $table->foreignId('replied_to_id')->nullable()->after('subject')->constrained('messages')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['replied_to_id']);
            $table->dropColumn(['read_at', 'subject', 'replied_to_id']);
        });
    }
};
