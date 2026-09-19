<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function ($table) {
            $table->string('scope')->default('shop')->after('code');
        });
        DB::statement("UPDATE vouchers SET scope = 'shop' WHERE scope IS NULL OR scope = ''");
    }

    public function down(): void
    {
        Schema::table('vouchers', function ($table) {
            $table->dropColumn('scope');
        });
    }
};
