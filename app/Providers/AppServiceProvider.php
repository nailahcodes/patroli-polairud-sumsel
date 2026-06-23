<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        App::setLocale('id');
        Carbon::setLocale('id');

        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

        Paginator::useBootstrapFive();
    }
}