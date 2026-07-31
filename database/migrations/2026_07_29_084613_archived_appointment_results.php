<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_appointment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archived_appointment_id');
            $table->unsignedBigInteger('original_appointment_id');
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('original_created_at')->nullable();
            $table->timestamp('original_updated_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();

            $table->index('archived_appointment_id');
            $table->index('original_appointment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_appointment_results');
    }
};