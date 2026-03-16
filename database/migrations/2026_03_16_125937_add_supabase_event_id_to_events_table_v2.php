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
        if (!Schema::hasColumn('events', 'supabase_event_id')) {
            Schema::table('events', function (Blueprint $table) {
                // Keep it simple for SQLite: add column only.
                $table->string('supabase_event_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'supabase_event_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('supabase_event_id');
            });
        }
    }
};
