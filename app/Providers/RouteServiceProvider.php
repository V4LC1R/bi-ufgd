<?php
namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            $this->mapModuleRoutes();
        });
    }

    protected function mapModuleRoutes(): void
    {
        $modulesPath = base_path('app/Modules');
        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);
            $routesPath = $modulePath . '/Routes';

            if (File::exists($routesPath . '/api.php')) {
                Route::prefix('api/' . strtolower($moduleName))
                    ->middleware('api')
                    ->group($routesPath . '/api.php');
            }
        }
    }
}
