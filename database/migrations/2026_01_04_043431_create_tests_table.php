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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();

            // Test Info
            $table->string('test_name'); // e.g. CBC, Urinalysis
            $table->string('category');  // Hematology, Chemistry, etc.
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->nullable();

            // Status (for enabling/disabling test)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }
};
