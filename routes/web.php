<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
#use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページとしてQRコードの表示ページを設定
#Route::view('/', 'ordering::help');