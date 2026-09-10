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
        Schema::table('orders', function (Blueprint $table) {
            // Thêm các cột dành riêng cho Admin
            $table->string('shipping_provider')->nullable(); // Lưu đơn vị vận chuyển
            $table->date('shipping_date')->nullable();       // Lưu ngày giao hàng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa đi nếu cần rollback
            $table->dropColumn(['shipping_provider', 'shipping_date']);
        });
    }
};