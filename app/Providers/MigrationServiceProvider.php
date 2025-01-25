<?php

namespace k1fl1k\joyart\Providers;

use Illuminate\Database\Grammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Grammar::macro('typeRaw', function (Fluent $column) {
            return $column->get('raw_type');
        });

        Blueprint::macro('typeColumn', function (string $type, string $columnName) {
            return $this->addColumn('raw', $columnName, ['raw_type' => $type]);
        });
    }
}
