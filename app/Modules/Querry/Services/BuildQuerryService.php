<?php

namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Services\ConnectionService;
use App\Modules\Querry\Builder\DimensionBuilder;
use App\Modules\Querry\Builder\FactBuilder;
use App\Modules\Querry\Builder\SubDimensionBuilder;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Traits\HasUsedDimensions;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BuildQuerryService
{

    use HasUsedDimensions;
    public function __construct(
        protected IStructTable $struct_service,
        protected BridgeJoinService $bridge
    ) {
    }

    public function makeQuerry(PreSqlDTO $pre_sql, string $hash, bool $dump = false): array
    {
        try {
            $tables = $this->getEntities($pre_sql);

            $this->bridge->resolve($pre_sql);

            $conn = $this->struct_service->swapToDataBase();

            $query = FactBuilder::fill($tables['fact'], $pre_sql->fact, $conn);
            DimensionBuilder::fill($tables['fact'], $pre_sql->dimensions, $query);
            SubDimensionBuilder::fill($tables['dimensions'], $pre_sql->subDimensions, $query);

            $cache = $this->cachingQuerry($hash, $query);

            $response = [
                'build' => true
            ];

            if ($dump)
                $response = array_merge($response, $cache);

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    private function cachingQuerry(string $key, Builder $query)
    {
        $sql = $query->toSql();
        $binds = $query->getBindings();

        Cache::put("$key-sql", $sql);
        Cache::put("$key-bindings", $binds);

        return [
            'sql' => $sql,
            'binds' => $binds
        ];
    }

}
