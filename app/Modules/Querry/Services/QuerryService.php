<?php
namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Constants\QuerryType;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Jobs\ProcessQuerryBuilder;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Services\ValidatePreSqlService;
use App\Modules\Querry\Traits\HasUsedDimensions;


class QuerryService
{

    use HasUsedDimensions;

    public function __construct(
        protected StructTable $struct_service,
        protected ValidatePreSqlService $validate_presql
    ) {
    }

    public function savePreSql(PreSqlDTO $pre_sql, $id = null)
    {
        $this->validate_presql->compare($this->getEntities($pre_sql), $pre_sql);

        if (count($this->validate_presql->getErrors()) > 0)
            throw new \Exception(json_encode($this->validate_presql->getErrors()));

        $this->dispatch($pre_sql);
    }

    private function dispatch(PreSqlDTO $pre_sql, $id = null)
    {
        $query = Querry::create([
            'connection_id' => $this->struct_service->getConnection()->id,
            'hash' => $this->generateStableHash($pre_sql),
            'type' => QuerryType::JSON,
            'struct' => json_encode($pre_sql->toArray()),
            'status' => QuerryStatusEnum::PENDING,
            'literal_query' => '',
            'binds' => json_encode([])
        ]);

        ProcessQuerryBuilder::dispatch($query->id);
    }

    private function generateStableHash(PreSqlDTO $dto): string
    {
        // Converte o DTO para um array
        $data = $dto->toArray();

        // Ordena o array recursivamente pelas chaves para garantir consistência
        // "ksort" ordena pelas chaves (keys)
        array_walk_recursive($data, function (&$value, $key) use (&$data) {
            if (is_array($value)) {
                ksort($value);
            }
        });
        ksort($data);

        // Codifica o array ordenado para JSON e gera o hash
        return hash('sha256', json_encode($data));
    }
}