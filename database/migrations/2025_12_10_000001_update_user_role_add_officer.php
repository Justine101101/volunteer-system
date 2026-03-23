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
            Schema::create('users_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('photo_url')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->enum('role', ['superadmin', 'admin', 'officer', 'volunteer'])->default('volunteer');
                $table->boolean('notification_pref')->default(true);
                $table->boolean('dark_mode')->default(false);
                $table->timestamps();
            });

            DB::statement("
                INSERT INTO users_new (id, name, email, photo_url, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_new', 'users');
            return;
        }

        // MySQL / MariaDB: alter enum definition
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','officer','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        // PostgreSQL: Laravel's enum->change() generates invalid SQL for CHECK constraints on existing columns.
        // Use explicit CHECK constraint SQL instead.
        DB::statement("
            DO $$
            DECLARE
                r RECORD;
            BEGIN
                -- If the constraint has the expected name, drop it explicitly first.
                IF EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'users_role_check'
                      AND conrelid = 'public.users'::regclass
                ) THEN
                    ALTER TABLE public.users DROP CONSTRAINT users_role_check;
                END IF;

                -- Drop any existing CHECK constraints that look like they enforce the users.role set
                FOR r IN
                    SELECT conname
                    FROM pg_constraint
                    WHERE conrelid = 'public.users'::regclass
                      AND contype = 'c'
                      AND pg_get_constraintdef(oid) ILIKE '%role%'
                      AND pg_get_constraintdef(oid) ILIKE '%volunteer%'
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

        // Add the updated constraint only if it's not already present.
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'users_role_check'
                      AND conrelid = 'public.users'::regclass
                ) THEN
                    ALTER TABLE public.users
                        ADD CONSTRAINT users_role_check
                        CHECK (role IN ('superadmin', 'admin', 'officer', 'volunteer'));
                END IF;
            END $$;
        ");
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
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->enum('role', ['superadmin', 'admin', 'volunteer'])->default('volunteer');
                $table->boolean('notification_pref')->default(true);
                $table->boolean('dark_mode')->default(false);
                $table->timestamps();
            });

            DB::statement("
                INSERT INTO users_old (id, name, email, photo_url, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at)
                SELECT id, name, email, photo_url, email_verified_at, password, remember_token, role, notification_pref, dark_mode, created_at, updated_at
                FROM users
            ");

            Schema::drop('users');
            Schema::rename('users_old', 'users');
            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('superadmin','admin','volunteer') NOT NULL DEFAULT 'volunteer'");
            return;
        }

        // PostgreSQL: revert CHECK constraint to remove 'officer'
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
                      AND pg_get_constraintdef(oid) ILIKE '%volunteer%'
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
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'users_role_check'
                      AND conrelid = 'public.users'::regclass
                ) THEN
                    ALTER TABLE public.users
                        ADD CONSTRAINT users_role_check
                        CHECK (role IN ('superadmin', 'admin', 'volunteer'));
                END IF;
            END $$;
        ");
    }
};

