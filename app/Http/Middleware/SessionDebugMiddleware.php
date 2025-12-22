<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SessionDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log sebelum request diproses
        if (str_contains($request->url(), 'settings') || str_contains($request->url(), 'profile')) {
            Log::info('SessionDebugMiddleware - Before processing request: ' . $request->method() . ' ' . $request->url());
            Log::info('Session ID: ' . session()->getId());
            Log::info('Auth status: ' . (auth()->check() ? 'logged in' : 'not logged in'));
            if (auth()->check()) {
                Log::info('User ID: ' . auth()->id());
            }
        }

        $response = $next($request);

        // Log setelah request diproses
        if (str_contains($request->url(), 'settings') || str_contains($request->url(), 'profile')) {
            Log::info('SessionDebugMiddleware - After processing request: ' . $request->method() . ' ' . $request->url());
            Log::info('Session ID: ' . session()->getId());
            Log::info('Auth status: ' . (auth()->check() ? 'logged in' : 'not logged in'));
            if (auth()->check()) {
                Log::info('User ID: ' . auth()->id());
            }
        }

        return $response;
    }
}