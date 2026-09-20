<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'voucher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_voucher_usages');
    }
};
