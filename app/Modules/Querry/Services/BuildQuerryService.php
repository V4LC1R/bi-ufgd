<?php

namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Querry\Builder\DimensionBuilder;
use App\Modules\Querry\Builder\FactBuilder;
use App\Modules\Querry\Builder\SubDimensionBuilder;
use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Traits\HasUsedDimensions;
use Illuminate\Database\Query\Builder;

class BuildQuerryService
{
    use HasUsedDimensions;
    public function __construct(
        protected StructTable $struct_service,
        protected BridgeJoinService $bridge,
        protected DynamicConnectionManager $conn_manager
    ) {
    }

    public function makeQuerry(Querry $pre_querry, bool $dump = false): array
    {
        try {
            $dto = new PreSqlDTO($pre_querry->struct);

            $tables = $this->getEntities($dto);
            $this->bridge->resolve($dto);

            $query = FactBuilder::fill($tables['fact'], $dto->fact, $this->getConnectionPool());
            DimensionBuilder::fill($tables['fact'], $dto->dimensions, $query);
            SubDimensionBuilder::fill($tables['dimensions'], $dto->subDimensions, $query);

            $this->storeProcess($pre_querry, $query);

            return $pre_querry->toArray();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    private function storeProcess(Querry $pre_querry, Builder $query)
    {
        $sql = $query->toSql();
        $binds = $query->getBindings();

        $pre_querry->binds = json_encode($binds);
        $pre_querry->literal_query = $sql;
        $pre_querry->status = QuerryStatusEnum::SUCCESS;

        $pre_querry->save();

        return [
            'sql' => $sql,
            'binds' => $binds
        ];
    }

    private function getConnectionPool()
    {
        return $this->conn_manager->setup($this->struct_service->getConnection());
    }

}
