<?php

namespace App\Providers;

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Connection\Services\RuntimeConnectionManager;
use App\Modules\Connection\Services\StructTableService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StructTable::class, StructTableService::class);
        $this->app->singleton(DynamicConnectionManager::class, RuntimeConnectionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
