<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE vouchers MODIFY type ENUM('fixed', 'percent', 'free_shipping') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE vouchers SET type = 'fixed' WHERE type = 'free_shipping'");
        DB::statement("ALTER TABLE vouchers MODIFY type ENUM('fixed', 'percent') NOT NULL");
    }
};
