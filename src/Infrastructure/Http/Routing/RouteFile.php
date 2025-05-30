<?php

namespace App\Infrastructure\Http\Routing;
use Illuminate\Routing\Route;

abstract class RouteFile
{
    protected $options;
    /**
     * @var Route
     */
    protected $router ;
    public function __construct($options = [])
    {
        $this->options = $options;

        $this->router = app('router');

        $this->register();
    }

    public function register()
    {
        \Route::pattern('id', '[0-9]+');

        $this->router->group($this->options, function () {
            $this->routes();
        });
    }

    abstract protected function routes();
    
}