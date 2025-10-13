<?php

namespace App\Modules\Query\Services; // Ou um novo módulo 'Dimension'

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Services\ConnectionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DimensionDataService
{
    public function __construct(
        protected DynamicConnectionManager $conn_manager
    ) {
    }

    public function getDimensionsRows($table_id, $data)
    {

    }
}