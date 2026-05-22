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
        Schema::table('whats_app_accounts', function (Blueprint $table) {
            $table->boolean('is_default')->default(false);
            $table->string('status')->nullable();
            $table->string('quality_rating')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('token_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'is_default',
                'status',
                'quality_rating',
                'last_synced_at',
                'token_expires_at'
            ]);
        });
    }
};
