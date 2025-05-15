<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Line\LineExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // SocialiteProviders の hook を登録
        SocialiteWasCalled::class => [
            LineExtendSocialite::class . '@handle',
        ],
    ];
}
