<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            // Supabase uses UUID primary keys for `users.id`, but some local setups use integer IDs.
            // Store as string and avoid a strict FK so migrations don't fail due to type mismatches.
            $table->string('user_id');
            $table->string('otp_code');        // hashed OTP
            $table->timestamp('expires_at');
            $table->timestamps();              // created_at + updated_at

            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};

