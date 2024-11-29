<?php

use Illuminate\Support\Facades\Route;

// セルフオーダーシステムのトップページの設定
Route::redirect('/', '/order');