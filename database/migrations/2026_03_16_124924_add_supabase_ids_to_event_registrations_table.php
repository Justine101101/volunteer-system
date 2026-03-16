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
        if (!Schema::hasColumn('event_registrations', 'supabase_registration_id')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->string('supabase_registration_id')->nullable()->after('id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('event_registrations', 'supabase_registration_id')) {
            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropColumn('supabase_registration_id');
            });
        }
    }
};
