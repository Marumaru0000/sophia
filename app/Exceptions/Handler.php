<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Throwable;

class Handler extends ExceptionHandler
{
    // 既存の設定...

    /**
     * 認証エラー時のリダイレクト先をガードごとに分岐
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // どのガードで落ちたかを取得
        $guard = Arr::get($exception->guards(), 0);

        // ガードごとにログインルートを振り分け
        switch ($guard) {
            case 'admin':
                $loginRoute = route('admin.login');
                break;
            case 'customer':
            default:
                $loginRoute = route('customer.login.line');
        }

        return redirect()->guest($loginRoute);
    }
}