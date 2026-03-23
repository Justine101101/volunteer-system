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

        // First, normalize any existing legacy value.
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);

        // SQLite: rebuild table to update CHECK constraint (remove superadmin from allowed roles)
        if ($driver === 'sqlite') {
            DB::statement("DROP TABLE IF EXISTS users_new");

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
                    role VARCHAR(255) NOT NULL DEFAULT 'volunteer' CHECK(role IN ('admin', 'president', 'officer', 'volunteer')),
                    notification_pref BOOLEAN NOT NULL DEFAULT 1,
                    dark_mode BOOLEAN NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ");

            DB::statement("
                INSERT INTO users_new (id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token,
                       CASE WHEN role = 'superadmin' THEN 'admin' ELSE role END,
                       notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_new', 'users');
            return;
        }

        // MySQL / MariaDB: alter enum definition
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','president','officer','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        // PostgreSQL: update CHECK constraint explicitly (Laravel enum->change() breaks on existing columns).
        DB::statement("
            DO $$
            DECLARE
                r RECORD;
            BEGIN
                -- Drop expected constraint name first (if present).
                IF EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'users_role_check'
                      AND conrelid = 'public.users'::regclass
                ) THEN
                    ALTER TABLE public.users DROP CONSTRAINT users_role_check;
                END IF;

                -- Defensive: drop any other role-related CHECK constraints.
                FOR r IN
                    SELECT conname
                    FROM pg_constraint
                    WHERE conrelid = 'public.users'::regclass
                      AND contype = 'c'
                      AND pg_get_constraintdef(oid) ILIKE '%role%'
                LOOP
                    EXECUTE 'ALTER TABLE public.users DROP CONSTRAINT ' || quote_ident(r.conname);
                END LOOP;
            END $$;
        ");

        DB::statement("
            ALTER TABLE public.users
                ALTER COLUMN role TYPE varchar(255),
                ALTER COLUMN role SET NOT NULL,
                ALTER COLUMN role SET DEFAULT 'volunteer'
        ");

        DB::statement("
            ALTER TABLE public.users
                ADD CONSTRAINT users_role_check
                CHECK (role IN ('admin', 'president', 'officer', 'volunteer'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // SQLite: rebuild table to re-allow superadmin
        if ($driver === 'sqlite') {
            DB::statement("DROP TABLE IF EXISTS users_old");

            DB::statement("
                CREATE TABLE users_old (
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
                INSERT INTO users_old (id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, phone, google_id, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_old', 'users');
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','president','officer','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        // PostgreSQL: restore CHECK constraint explicitly.
        DB::statement("
            DO $$
            DECLARE
                r RECORD;
            BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'users_role_check'
                      AND conrelid = 'public.users'::regclass
                ) THEN
                    ALTER TABLE public.users DROP CONSTRAINT users_role_check;
                END IF;

                FOR r IN
                    SELECT conname
                    FROM pg_constraint
                    WHERE conrelid = 'public.users'::regclass
                      AND contype = 'c'
                      AND pg_get_constraintdef(oid) ILIKE '%role%'
                LOOP
                    EXECUTE 'ALTER TABLE public.users DROP CONSTRAINT ' || quote_ident(r.conname);
                END LOOP;
            END $$;
        ");

        DB::statement("
            ALTER TABLE public.users
                ALTER COLUMN role TYPE varchar(255),
                ALTER COLUMN role SET NOT NULL,
                ALTER COLUMN role SET DEFAULT 'volunteer'
        ");

        DB::statement("
            ALTER TABLE public.users
                ADD CONSTRAINT users_role_check
                CHECK (role IN ('superadmin', 'admin', 'president', 'officer', 'volunteer'))
        ");
    }
};

