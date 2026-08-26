<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_results', function (Blueprint $table) {
            $table->dropColumn('parameter_name');
        });
    }

    public function down(): void
    {
        Schema::table('appointment_results', function (Blueprint $table) {
            $table->string('parameter_name');
        });
    }
};