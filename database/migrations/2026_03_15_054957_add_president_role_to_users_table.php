<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // SQLite: rebuild table to update CHECK constraint
        if ($driver === 'sqlite') {
            // Drop users_new if it exists from a previous failed migration
            DB::statement("DROP TABLE IF EXISTS users_new");
            
            // Drop the old table and recreate with new enum
            DB::statement("
                CREATE TABLE users_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    photo_url VARCHAR(255) NULL,
                    phone VARCHAR(30) NULL,
                    google_id VARCHAR(255) NULL,
                    email_verified_at TIMESTAMP NULL,
                    password VARCHAR(255) NOT NULL,
                    remember_token VARCHAR(100) NULL,
                    role VARCHAR(255) NOT NULL DEFAULT 'volunteer' CHECK(role IN ('superadmin', 'admin', 'president', 'officer', 'volunteer')),
                    notification_pref BOOLEAN NOT NULL DEFAULT 1,
                    dark_mode BOOLEAN NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ");

            DB::statement("
                INSERT INTO users_new (id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_new', 'users');
            return;
        }

        // MySQL / MariaDB: alter enum definition
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','president','officer','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        // PostgreSQL (enum backed by CHECK constraint in SQLite-like style)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'president', 'officer', 'volunteer'])->default('volunteer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::create('users_old', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('photo_url')->nullable();
                $table->string('phone')->nullable();
                $table->string('google_id')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->enum('role', ['superadmin', 'admin', 'officer', 'volunteer'])->default('volunteer');
                $table->boolean('notification_pref')->default(true);
                $table->boolean('dark_mode')->default(false);
                $table->timestamps();
            });

            DB::statement("
                INSERT INTO users_old (id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_old', 'users');
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','officer','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'officer', 'volunteer'])->default('volunteer')->change();
        });
    }
};
