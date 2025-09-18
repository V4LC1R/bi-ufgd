<?php

namespace App\Providers;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Services\StructTableService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IStructTable::class, StructTableService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
