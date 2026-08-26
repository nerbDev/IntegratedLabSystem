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
        Schema::create('appointment_results', function (Blueprint $table) {
            $table->id();
            
            // This MUST come after the appointments table is created
            $table->foreignId('appointment_id')
                  ->constrained('appointments') 
                  ->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('category')->nullable(); 
            $table->string('parameter_name');
            $table->string('result_value');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->boolean('is_abnormal')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_results');
    }
};