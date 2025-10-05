<?php
namespace App\Modules\Querry\Traits;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;

trait HasUsedDimensions
{
   /**
     * Summary of getOnlyUsedDimensions
     * @param array<string,TableDTO> $dimensions_table
     * @param array<DimensionDTO> $dimensions_pre_sql
     * @return array<string,TableDTO>
     */
    private function getOnlyUsedDimensions(array $dimensions_table, array $dimensions_pre_sql)
    {
        $used_dimensions = [];
        foreach ($dimensions_pre_sql as $pre) {
            if(!array_key_exists($pre->table,$dimensions_table))
                continue;

            $used_dimensions[$pre->table]= $dimensions_table[$pre->table];
        }
        return $used_dimensions;
    }

    private function getEntities(PreSqlDTO $pre_sql)
    {
        if(!$this->struct_service)
            throw new \Exception('Struct service not found');

        if(! $this->struct_service instanceof  IStructTable)
            throw new \Exception('Service register is not IStructTable');

        $this->struct_service->setConnectionName($pre_sql->connectionName);

        $tables = $this->struct_service->getStructConnection();

        return [
            'fact' => array_values($tables['fact'])[0],
            'dimensions' =>$tables['dimension'],
            'sub-dimensions' => $tables['sub-dimension'],
        ];
    }

}