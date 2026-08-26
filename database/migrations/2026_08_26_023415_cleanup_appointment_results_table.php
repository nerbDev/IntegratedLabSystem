<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_results', function (Blueprint $table) {
            $table->dropColumn(['category', 'result_value', 'unit', 'reference_range', 'is_abnormal']);
        });
    }

    public function down(): void
    {
        Schema::table('appointment_results', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('result_value');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->boolean('is_abnormal')->default(false);
        });
    }
};