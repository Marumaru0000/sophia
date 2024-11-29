<?php

use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページとしてQRコードの表示ページを設定
#Route::view('/', 'ordering::help');
Route::view('/', 'ordering::order.index')->name('order');
