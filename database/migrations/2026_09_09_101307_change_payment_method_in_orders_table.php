<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mở rộng cột payment_method thành kiểu chuỗi để nhận mọi phương thức (PAYOS, MOMO...)
        DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(50) DEFAULT 'COD'");
    }

    public function down(): void
    {
        // Không cần viết lệnh down
    }
};