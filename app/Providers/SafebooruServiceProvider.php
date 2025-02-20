<?php

namespace k1fl1k\joyart\Providers;

use Illuminate\Support\ServiceProvider;
use k1fl1k\joyart\Services\SafebooruService;

class SafebooruServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SafebooruService::class, function () {
            return new SafebooruService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
