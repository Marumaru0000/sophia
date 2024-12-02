<?php

use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページの設定
Route::redirect('/', '/order');

Route::get('order/{table?}', function () {
    return view('ordering::order.index', [
        'table' => request('table'),
    ]);
})->name('order');

Route::get('/debug', function () {
    return response()->json([
        'PAYPAY_API_KEY' => env('PAYPAY_API_KEY'),
        'PAYPAY_API_SECRET' => env('PAYPAY_API_SECRET'),
        'PAYPAY_MERCHANT_ID' => env('PAYPAY_MERCHANT_ID'),
    ]);
});
Route::get('/paypay-test', function () {
    try {
        $response = PayPay::code()->getPaymentDetails('test-payment-id');
        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
