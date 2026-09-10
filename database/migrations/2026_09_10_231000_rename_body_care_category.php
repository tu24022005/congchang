<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->where('name', 'Chăm sóc cơ thể')
            ->update(['name' => 'Chăm sóc cá nhân', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('categories')
            ->where('name', 'Chăm sóc cá nhân')
            ->update(['name' => 'Chăm sóc cơ thể', 'updated_at' => now()]);
    }
};