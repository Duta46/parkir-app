<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // require base_path('routes/console.php');

        // Force HTTPS in production or when using ngrok
        // if(config('app.env') === 'production' || str_contains(request()->getHost(), 'ngrok-free.app')) {
        //     URL::forceScheme('https');

        //     // Also set secure headers for ngrok
        //     $this->app['request']->server->set('HTTPS', 'on');
        //     $this->app['request']->server->set('HTTP_X_FORWARDED_PROTO', 'https');
        // }
    }
}
