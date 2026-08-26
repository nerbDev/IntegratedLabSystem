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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->unsignedBigInteger('patient_id');

            // Appointment Details
            $table->date('appointment_date'); // renamed from 'date'
            $table->time('appointment_time'); // renamed from 'time'
            $table->string('appointment_type')->nullable(); // walk-in, online, home service
            $table->string('service')->nullable(); // new: matches your form field




            // Patient Info (new fields from form)
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('municipality');
            $table->string('barangay');
            $table->string('street_details');
            $table->string('landmark');

            // Optional branch (if applicable)
            $table->unsignedBigInteger('branch_id')->nullable();

            // Status Tracking
            $table->enum('status', [
                'pending',
                'approved',
                'rescheduled',
                'completed',
                'cancelled',
                'released',
            ])->default('pending');

            // Extra Info
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('patient_id')->references('id')->on('useraccount')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }
};