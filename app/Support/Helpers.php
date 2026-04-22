<?php

use App\Data\ActorData;
use Illuminate\Support\Facades\App;

if (!function_exists('actor')) {
    function actor(): ?ActorData
    {
        return App::bound('actor')
            ? App::make('actor')
            : null;
    }
}