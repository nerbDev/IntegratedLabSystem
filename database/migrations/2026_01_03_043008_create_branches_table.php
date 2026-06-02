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
    Schema::create('branches', function (Blueprint $table) {
        $table->id(); // IMPORTANT: matches foreign key type
        $table->string('branch_name');
        $table->string('address')->nullable();
        $table->string('contact_number')->nullable();
        $table->timestamps();
    });
}
};
