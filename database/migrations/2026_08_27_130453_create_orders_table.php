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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Đã mở rộng sức chứa lên (20, 2) để không bị lỗi "Out of range" khi giỏ hàng có giá trị lớn
            $table->decimal('total', 20, 2);
            
            // Trạng thái đơn hàng chuẩn theo yêu cầu Lab 05B
            $table->enum('status', [
                'processing', 
                'paid', 
                'cancelled', 
                'chờ thanh toán', 
                'đã thanh toán (Chuyển khoản Ngân hàng VietQR (VPBank))', 
                'thanh toán Chuyển khoản Ngân hàng VietQR (VPBank) không thành công'
            ])->default('processing');
            
            // Đổi thành string theo đúng chuẩn Lab 05B
            $table->string('payment_method')->default('COD');

            // 3 Cột lưu thông tin khách hàng (Tên, SĐT, Địa chỉ) 
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};