<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/order');
});

// セルフオーダーシステムのトップページとしてQRコードの表示ページを設定
#Route::view('/', 'ordering::help');
Route::view('/', 'ordering::order.index')->name('order');
