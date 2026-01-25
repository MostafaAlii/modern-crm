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
        Schema::create('product_plan_features', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('plan_id')->constrained('product_plans')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('product_module_features')->onDelete('cascade');
            $table->boolean('is_included')->default(true);
            $table->integer('limit_value')->nullable(); // NULL = unlimited
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['plan_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_plan_features');
    }
};
