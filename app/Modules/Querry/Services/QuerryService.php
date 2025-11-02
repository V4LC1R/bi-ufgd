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

    public function getQuery(string $id)
    {
        return Querry::findOrFail($id)->struct;
    }

    public function savePreSql(PreSqlDTO $pre_sql, $id = null): Querry
    {
        $this->validate($pre_sql);

        return $this->dispatch($pre_sql, $id);
    }

    public function validate(PreSqlDTO $pre_sql): void
    {
        $this->validate_presql->compare($this->getEntities($pre_sql), $pre_sql);

        $errors = $this->validate_presql->getErrors();
        if (count($errors) > 0) {
            // Lança uma exceção específica com os erros, que é uma prática melhor.
            throw new \Exception(json_encode($this->validate_presql->getErrors()));
        }
    }

    private function dispatch(PreSqlDTO $pre_sql, ?int $id = null): Querry
    {

        $query = Querry::updateOrCreate(
            ['id' => $id], // Chave para encontrar
            [ // Dados para criar ou atualizar
                'connection_id' => $this->struct_service->getConnection()->id,
                'description' => $pre_sql->description,
                'hash' => $this->generateStableHash($pre_sql),
                'type' => QuerryType::JSON,
                'struct' => $pre_sql->toArray(),
                'status' => QuerryStatusEnum::PENDING,
            ]
        );

        ProcessQuerryBuilder::dispatch($query->id);

        return $query;
    }

    private function generateStableHash(PreSqlDTO $dto): string
    {
        $data = $dto->toArray();
        array_walk_recursive($data, function (&$value, $key) use (&$data) {
            if (is_array($value))
                ksort($value);
        });
        ksort($data);
        return hash('sha256', json_encode($data));
    }
}