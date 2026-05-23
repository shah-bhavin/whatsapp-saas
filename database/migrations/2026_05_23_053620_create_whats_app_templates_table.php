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
        Schema::create('whats_app_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whats_app_account_id')->constrained()->cascadeOnDelete();
            $table->string('template_name');
            $table->string('meta_template_id')->nullable();
            $table->string('category');
            $table->string('language')->default('en');
            $table->string('status')->default('PENDING');
            $table->text('header_text')->nullable();
            $table->longText('body_text');
            $table->text('footer_text')->nullable();
            $table->json('buttons')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('has_media')->default(false);
            $table->string('media_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_templates');
    }
};
