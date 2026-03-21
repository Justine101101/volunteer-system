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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['superadmin', 'admin', 'volunteer'])->default('volunteer');
                }
                if (!Schema::hasColumn('users', 'notification_pref')) {
                    $table->boolean('notification_pref')->default(true);
                }
                if (!Schema::hasColumn('users', 'dark_mode')) {
                    $table->boolean('dark_mode')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $toDrop = [];
                if (Schema::hasColumn('users', 'role')) {
                    $toDrop[] = 'role';
                }
                if (Schema::hasColumn('users', 'notification_pref')) {
                    $toDrop[] = 'notification_pref';
                }
                if (Schema::hasColumn('users', 'dark_mode')) {
                    $toDrop[] = 'dark_mode';
                }
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }
    }
};
