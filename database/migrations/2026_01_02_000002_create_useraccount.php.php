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
            $table->string('middle_name')->nullable()->change();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable()->change();
            $table->enum('sex', ['male', 'female'])->nullable()->change();
            $table->string('Umunicipality')->nullable()->change();
            $table->string('Ubarangay')->nullable()->change();
            $table->string('Ustreet_house')->nullable()->change();

            // Contact Information
            $table->string('phone_number')->nullable()->change();
            $table->string('email')->unique()->nullable()->change();

            // Emergency Contact
            $table->string('contact_person')->nullable()->change();
            $table->string('contact_number')->nullable()->change();

            // Login Information
            $table->string('password')->nullable()->change();

            // Laravel timestamps
            $table->timestamps()->nullable()->change();
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