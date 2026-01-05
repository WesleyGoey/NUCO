<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
        // Force HTTPS in production and fix session domain (Railway)
        if ($this->app->environment('production')) {
            try {
                URL::forceScheme('https');
                $host = parse_url(config('app.url'), PHP_URL_HOST);
                if ($host) {
                    config(['session.domain' => $host]);
                }
            } catch (\Exception $e) {
                Log::warning('AppServiceProvider: failed to force https / set session domain: '.$e->getMessage());
            }
        }

        // Share data with all views — make safe: check schema & class and catch DB errors
        view()->composer('*', function ($view) {
            try {
                if (Auth::check()) {
                    $pendingApplication = null;

                    if (class_exists(\App\Models\ManagerApplication::class) && Schema::hasTable('manager_applications')) {
                        $pendingApplication = \App\Models\ManagerApplication::where('user_id', Auth::id())
                            ->where('status', 'pending')
                            ->first();
                    }

                    $view->with('userPendingApplication', $pendingApplication);
                }
            } catch (\Exception $e) {
                // Prevent view rendering from failing due to DB/other errors
                Log::warning('View composer (ManagerApplication) failed: '.$e->getMessage());
                $view->with('userPendingApplication', null);
            }
        });
    }
}
