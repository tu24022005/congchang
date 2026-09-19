<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('loyalty_points')->default(0)->after('role');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('scope')->constrained()->nullOnDelete();
        });

        Schema::create('loyalty_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('points');
            $table->string('description');
            $table->timestamps();
            $table->unique(['user_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_transactions');
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('loyalty_points');
        });
    }
};
