<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // e.g. SAVE20
            $table->enum('type', ['fixed', 'percentage']); // fixed = Tk off, percentage = % off
            $table->decimal('value', 10, 2);            // amount or percent
            $table->decimal('min_order', 10, 2)->default(0); // minimum cart value required
            $table->decimal('max_discount', 10, 2)->nullable(); // cap on percentage discounts
            $table->unsignedInteger('max_uses')->nullable();    // null = unlimited
            $table->unsignedInteger('times_used')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
