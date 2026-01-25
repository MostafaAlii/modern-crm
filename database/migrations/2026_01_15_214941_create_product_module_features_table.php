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
        Schema::create('product_module_features', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('module_id')->constrained('product_modules')->onDelete('cascade');
            $table->string('slug');
            $table->enum('feature_type', ['basic', 'advanced', 'premium'])->default('basic');
            $table->json('settings')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['module_id', 'slug']);
        });

        Schema::create('product_module_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_module_feature_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['product_module_feature_id', 'locale'], 'pmf_translation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_module_feature_translations');
        Schema::dropIfExists('product_module_features');
    }
};
