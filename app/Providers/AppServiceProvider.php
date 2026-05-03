<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS kalau pakai ngrok atau proxy
        $request = request();
        if ($request->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            $request->server('HTTPS') === 'on') {
            URL::forceScheme('https');
        }

        // Pakai Bootstrap untuk pagination
        Paginator::useBootstrap();
    }
}