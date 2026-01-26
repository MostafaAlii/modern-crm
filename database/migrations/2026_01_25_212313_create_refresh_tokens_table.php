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
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('token_id', 255)->unique();
            $table->string('access_token_id', 255)->nullable()->comment('JWT ID (jti claim)');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->text('device_info')->nullable()->comment('Device name, OS, etc.');
            $table->string('ip_address', 45)->nullable()->comment('IPv4 or IPv6');
            $table->text('user_agent')->nullable()->comment('Browser/App info');
            $table->string('location')->nullable()->comment('Geographic location from IP');
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('expires_at');
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedBigInteger('revoked_by')->nullable()->comment('Admin who revoked it');
            $table->json('metadata')->nullable()->comment('Additional custom data');
            $table->timestamps();
            $table->index(['user_id', 'user_type']);
            $table->index(['token_id', 'is_revoked']);
            $table->index(['expires_at', 'is_revoked']);
            $table->index(['user_id', 'user_type', 'is_revoked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
