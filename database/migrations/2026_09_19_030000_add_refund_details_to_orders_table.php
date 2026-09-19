<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refund_bank_name')->nullable()->after('payment_method');
            $table->string('refund_account_number')->nullable()->after('refund_bank_name');
            $table->string('refund_account_holder')->nullable()->after('refund_account_number');
            $table->string('refund_reference')->nullable()->after('refund_account_holder');
            $table->text('refund_note')->nullable()->after('refund_reference');
            $table->timestamp('refunded_at')->nullable()->after('refund_note');
            $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropColumn([
                'refund_bank_name',
                'refund_account_number',
                'refund_account_holder',
                'refund_reference',
                'refund_note',
                'refunded_at',
                'refunded_by',
            ]);
        });
    }
};
