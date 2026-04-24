<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        $compiledPath = config('view.compiled');

        if (is_string($compiledPath) && $compiledPath !== '') {
            File::ensureDirectoryExists($compiledPath);
        }

        if (config('security.force_https')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('auth-login', function (Request $request): Limit {
            $email = strtolower((string) $request->input('email', 'guest'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('auth-register', function (Request $request): Limit {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('password-reset-links', function (Request $request): Limit {
            $email = strtolower((string) $request->input('email', 'guest'));

            return Limit::perMinute(3)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('reservation-store', function (Request $request): Limit {
            return Limit::perMinute(8)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
