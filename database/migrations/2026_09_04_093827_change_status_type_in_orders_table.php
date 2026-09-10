<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ép kiểu cột status thành VARCHAR(255) để lưu được mọi trạng thái mới
        DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) DEFAULT 'processing'");
    }

    public function down(): void
    {
        // 
    }
};