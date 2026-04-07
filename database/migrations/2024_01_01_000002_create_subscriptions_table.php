<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('plan_name', ['free', 'pro'])->default('free');
            $table->unsignedInteger('request_limit')->default(20);
            $table->unsignedInteger('used_requests')->default(0);
            $table->timestamp('reset_at')->nullable()->comment('Monthly reset timestamp');
            $table->timestamps();

            $table->index(['user_id', 'plan_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
