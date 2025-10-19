<?php

namespace App\Modules\Connection\Contracts;

use App\Modules\Connection\Models\Connection;
use App\Modules\Querry\Models\Querry;

interface QueryExecutor
{
    public function executeAndCache(Querry $query);
}