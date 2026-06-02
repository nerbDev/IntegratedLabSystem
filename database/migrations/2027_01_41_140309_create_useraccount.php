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
        Schema::create('useraccount', function (Blueprint $table) {
            $table->id();

            // Role-based access
            $table->enum('role', ['patient', 'staff', 'admin'])->default('patient');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('sex', ['male', 'female']);
            $table->string('Umunicipality');
            $table->string('Ubarangay');
            $table->string('Ustreet_house');

            // Contact Information
            $table->string('phone_number');
            $table->string('email')->unique();

            // Emergency Contact
            $table->string('contact_person');
            $table->string('contact_number');

            // Login Information
            $table->string('password');

            // Laravel timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('useraccount');
    }
};