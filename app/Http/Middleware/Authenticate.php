<?php
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
{
    if (! $request->expectsJson()) {
        // お客様向け
        if ($request->is('customer/*')) {
            return route('customer.login.line');
        }
        // 管理画面向け
        if ($request->is('admin/*')) {
            return route('admin.login');
        }
        // 万が一のフォールバック
        return route('customer.login.line');
    }
    return route('customer.login.line');
}


}