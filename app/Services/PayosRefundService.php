<?php

namespace App\Services;

use App\Models\Order;
use PayOS\PayOS;

class PayosRefundService
{
    public function refund(Order $order): string
    {
        if ($order->payment_method !== 'PAYOS') {
            throw new \RuntimeException('Chỉ hỗ trợ hoàn tiền tự động cho đơn thanh toán PayOS.');
        }

        if (!$order->refund_bank_bin || !$order->refund_account_number) {
            throw new \RuntimeException('Thiếu mã BIN ngân hàng hoặc số tài khoản nhận hoàn tiền.');
        }

        $payOS = new PayOS(
            env('PAYOS_CLIENT_ID'),
            env('PAYOS_API_KEY'),
            env('PAYOS_CHECKSUM_KEY')
        );

        $response = $payOS->payouts->create([
            'referenceId' => 'REFUND-' . $order->id,
            'amount' => (int) $order->total,
            'description' => 'Hoan tien don #' . $order->id,
            'toBin' => $order->refund_bank_bin,
            'toAccountNumber' => $order->refund_account_number,
        ], 'refund-order-' . $order->id, ['asArray' => true]);

        $reference = is_array($response)
            ? ($response['data']['referenceId'] ?? $response['referenceId'] ?? null)
            : ($response->referenceId ?? null);

        if (!$reference) {
            throw new \RuntimeException('PayOS không trả về mã giao dịch hoàn tiền.');
        }

        return (string) $reference;
    }
}
