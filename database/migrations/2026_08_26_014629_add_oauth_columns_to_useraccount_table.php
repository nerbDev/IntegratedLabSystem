<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('useraccount', function (Blueprint $table) {
            $table->string('oauth_provider')->nullable()->after('password');
            $table->string('oauth_uid')->nullable()->after('oauth_provider');
        });
    }

    public function down(): void
    {
        Schema::table('useraccount', function (Blueprint $table) {
            $table->dropColumn(['oauth_provider', 'oauth_uid']);
        });
    }
};