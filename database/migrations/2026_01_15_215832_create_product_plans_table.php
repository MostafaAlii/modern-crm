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
        Schema::create('product_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('slug');
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', [
                'monthly',
                'quarterly',
                'semi_annual',
                'annual',
                'lifetime'
            ])->default('monthly')->comment('تكرار دورة الفواتير: شهري، ربع سنوي، نصف سنوي، سنوي، مدى الحياة');
            $table->integer('max_users')->nullable()->comment('Maximum number of allowed users. NULL means unlimited users.');
            $table->integer('storage_gb')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'slug']);
        });

        Schema::create('product_plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_plan_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['product_plan_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_plan_translations');
        Schema::dropIfExists('product_plans');
    }
};
