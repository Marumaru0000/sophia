<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Admin;
use Revolution\PayPay\Facades\PayPay;
use Revolution\Ordering\Http\Livewire\Order\Menus;
use Revolution\Ordering\Http\Livewire\Order\Prepare;
use Revolution\Ordering\Http\Livewire\Order\History;
use Revolution\Ordering\Http\Livewire\Order\PayPayCallback;

Route::get('/login', fn() => redirect()->route('customer.login.line'))
     ->name('login');
/*
|--------------------------------------------------------------------------
| 管理者用ログイン
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('web')->group(function () {
    // ログインフォーム
    Route::get('login', fn() => view('auth.login'))
         ->middleware('guest:admin')
         ->name('admin.login');

    // ログイン処理
    Route::post('admin/login', function (Request $request) {
        $pw = $request->input('password');
        if ($pw !== env('ORDERING_ADMIN_PASSWORD')) {
            return back()->withErrors(['password'=>'パスワードが違います']);
        }
        $admin = Admin::first() ?: Admin::create(['email'=>'admin']);
        Auth::guard('admin')->login($admin);
        return redirect()->intended(route('admin.dashboard'));
    })->name('admin.login.submit');

    // ログアウト
    Route::post('logout', function (Request $request) {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('admin.logout');

    // ダッシュボード（認証必須）
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', function () {
            return view('vendor.ordering.dashboard');
        })->name('admin.dashboard');
    });
});
/*
|--------------------------------------------------------------------------
| お客様用ログイン／注文フロー
|--------------------------------------------------------------------------
*/

Route::prefix('customer')->middleware('web')->group(function () {
    // 未認証のお客様
    Route::middleware('guest:customer')->group(function () {
        Route::get('login/line', function () {
            return Socialite::driver('line')->redirect();
        })->name('customer.login.line');

        Route::get('login/line/callback', function () {
            $social = Socialite::driver('line')->stateless()->user();
            $user = User::firstOrCreate(
                ['line_user_id' => $social->getId()],
                ['name'          => $social->getName(),
                 'avatar_url'    => $social->getAvatar()]
            );
            Auth::guard('customer')->login($user);
            return redirect()->route('customer.order');
        })->name('customer.login.line.callback');
    });

    // 認証済みのお客様
    Route::middleware('auth:customer')->group(function () {
        Route::get('order/{table?}', Menus::class)           ->name('customer.order');
        Route::get('confirm',       Prepare::class)         ->name('customer.prepare');
        Route::get('history',       History::class)         ->name('customer.history');
        Route::get('paypay/callback', PayPayCallback::class)->name('paypay.callback');

        // 顧客ログアウト
        Route::post('logout', function (Request $request) {
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('customer.login.line');
        })->name('customer.logout');
    });
});

// トップページ
Route::get('/', function () {
    return Auth::guard('customer')->check()
        ? redirect()->route('customer.order')
        : redirect()->route('customer.login.line');
});