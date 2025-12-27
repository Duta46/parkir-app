<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;

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
        require base_path('routes/console.php');

        // Force HTTPS in production or when using ngrok or Cloudflare tunnel
        if(config('app.env') === 'production' || str_contains(request()->getHost(), 'ngrok-free.app') || str_contains(request()->getHost(), 'trycloudflare.com')) {
            URL::forceScheme('https');

            // Also set secure headers for ngrok/Cloudflare tunnel
            $this->app['request']->server->set('HTTPS', 'on');
            $this->app['request']->server->set('HTTP_X_FORWARDED_PROTO', 'https');
        }

        // Handle asset URLs properly when behind proxy
        if (str_contains(request()->getHost(), 'trycloudflare.com')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        }
    }
}
