<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    $request = request();
    
    if ($request->server('HTTP_X_FORWARDED_PROTO') === 'https' || 
        $request->server('HTTPS') === 'on') {
        URL::forceScheme('https');
    }
}
}