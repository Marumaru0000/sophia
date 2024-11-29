<?php

use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページの設定
Route::redirect('/', '/order');

Route::get('order/{table?}', function () {
    return view('ordering::order.index', [
        'table' => request('table'),
    ]);
})->name('order');
