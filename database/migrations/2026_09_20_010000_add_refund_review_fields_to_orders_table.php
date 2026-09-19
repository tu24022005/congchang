<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refund_status')->nullable()->after('refund_note');
            $table->text('refund_rejection_note')->nullable()->after('refund_status');
            $table->timestamp('refund_reviewed_at')->nullable()->after('refund_rejection_note');
            $table->foreignId('refund_reviewed_by')->nullable()->after('refund_reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['refund_reviewed_by']);
            $table->dropColumn(['refund_status', 'refund_rejection_note', 'refund_reviewed_at', 'refund_reviewed_by']);
        });
    }
};
