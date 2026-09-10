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
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Khách hàng nào đang chat
        $table->text('message'); // Nội dung tin nhắn
        $table->boolean('is_admin')->default(false); // Phân biệt tin này của Admin hay của Khách
        $table->boolean('is_read')->default(false); // Trạng thái đã xem
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
