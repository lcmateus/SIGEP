<?php

namespace App\Providers;

use App\Auth\DualUserProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
        Auth::provider('dual', function ($app, array $config) {
            return new DualUserProvider();
        });

        RateLimiter::for('recuperar', fn (Request $request) => Limit::perHour(3)->by((string) $request->input('siape')));

        RateLimiter::for('codigo', fn (Request $request) => [
            Limit::perMinutes(10, 5)->by($request->ip()),
            Limit::perMinutes(10, 8)->by((string) $request->session()->get('recuperacao.siape')),
        ]);
    }
}