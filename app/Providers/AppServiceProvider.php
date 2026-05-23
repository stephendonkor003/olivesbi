<?php

namespace App\Providers;

use AlhajiAki\Sms\Sms;
use App\Services\MNotifySmsSender;
use App\Services\PyweSmsSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sms::extend('mnotify', function (array $config = []) {
            return new MNotifySmsSender($config);
        });

        Sms::extend('pywe', function (array $config = []) {
            return new PyweSmsSender($config);
        });
    }
}
