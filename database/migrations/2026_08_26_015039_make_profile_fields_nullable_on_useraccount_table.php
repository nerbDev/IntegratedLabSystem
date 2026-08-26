<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('useraccount', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->change();
            $table->enum('sex', ['male', 'female'])->nullable()->change();
            $table->string('Umunicipality')->nullable()->change();
            $table->string('Ubarangay')->nullable()->change();
            $table->string('Ustreet_house')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('contact_person')->nullable()->change();
            $table->string('contact_number')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('useraccount', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
            $table->enum('sex', ['male', 'female'])->nullable(false)->change();
            $table->string('Umunicipality')->nullable(false)->change();
            $table->string('Ubarangay')->nullable(false)->change();
            $table->string('Ustreet_house')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->string('contact_person')->nullable(false)->change();
            $table->string('contact_number')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};