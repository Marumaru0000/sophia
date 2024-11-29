<?php

use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページとしてQRコードの表示ページを設定
Route::redirect('/', '/order');