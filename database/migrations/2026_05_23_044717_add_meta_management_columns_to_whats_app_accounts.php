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
            $table->boolean('webhook_subscribed')->default(false);
            $table->boolean('token_valid')->default(true);
            $table->string('messaging_limit')->nullable();
            $table->string('account_review_status')->nullable();
            $table->text('last_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whats_app_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'webhook_subscribed',
                'token_valid',
                'messaging_limit',
                'account_review_status',
                'last_error',
            ]);
        });
    }
};
