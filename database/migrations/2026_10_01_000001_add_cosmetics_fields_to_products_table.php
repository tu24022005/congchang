<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('ingredients')->nullable()->after('description');
            $table->text('usage_instructions')->nullable()->after('ingredients');
            $table->json('skin_types')->nullable()->after('usage_instructions');
            $table->string('expiry_info')->nullable()->after('skin_types');
            $table->string('origin')->nullable()->after('expiry_info');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['ingredients', 'usage_instructions', 'skin_types', 'expiry_info', 'origin']);
        });
    }
};
