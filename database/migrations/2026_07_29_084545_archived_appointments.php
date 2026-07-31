<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_appointment_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('service');
            $table->string('appointment_type');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('municipality');
            $table->string('barangay');
            $table->string('street_details');
            $table->string('landmark');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamp('original_created_at')->nullable();
            $table->timestamp('original_updated_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();

            $table->index('original_appointment_id');
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_appointments');
    }
};