<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('inventory_logs')
            ->where('type', 'out')
            ->where('reference_type', 'App\\Models\\Order')
            ->whereNotNull('user_id')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('orders')
                    ->whereColumn('orders.id', 'inventory_logs.reference_id')
                    ->whereColumn('orders.user_id', 'inventory_logs.user_id');
            })
            ->update(['user_id' => null]);
    }

    public function down(): void
    {
        // The original customer assignment cannot be restored safely.
    }
};
