<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * このプロバイダのブートメソッド。
     */
    public function boot(): void
    {
        $this->routes(function () {
            // api.php のルート定義（必要なら）
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // web.php のルート定義
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
