<?php

namespace App\Providers;

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Contracts\FieldRelationResult;
use App\Modules\Connection\Contracts\QueryExecutor;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Connection\Services\AfterExecutionProcessService;
use App\Modules\Connection\Services\ExecuteSqlService;
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
        $this->app->singleton(QueryExecutor::class, ExecuteSqlService::class);
        $this->app->singleton(DynamicConnectionManager::class, RuntimeConnectionManager::class);
        $this->app->singleton(FieldRelationResult::class, AfterExecutionProcessService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
