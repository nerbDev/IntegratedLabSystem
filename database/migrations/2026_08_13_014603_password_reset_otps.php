<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp'); // hashed OTP, never stored in plain text
            $table->unsignedTinyInteger('attempts')->default(0); // failed verify attempts
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable(); // set once OTP is confirmed valid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};