<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

// Khi bên thứ 3 (như SePay/PayOS) nhận được tiền, họ sẽ tự động bắn 1 lệnh POST vào link này:
// URL của bạn sẽ là: http://127.0.0.1:8000/api/webhook/payment
Route::post('/webhook/payment', [WebhookController::class, 'handleBankWebhook']);