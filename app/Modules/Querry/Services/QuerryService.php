<?php
namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Constants\QuerryType;
use App\Modules\Querry\Exceptions\QueryValidationException; // Exceção customizada
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

    /**
     * Ponto de entrada principal. Orquestra a validação e o dispatch.
     * A ASSINATURA DESTE MÉTODO NÃO FOI ALTERADA, como você pediu.
     */
    public function savePreSql(PreSqlDTO $pre_sql, $id = null): Querry
    {
        // 1. Chama o método de validação reutilizável.
        // Se a validação falhar, ele lança uma exceção e para aqui.
        $this->validate($pre_sql);

        return $this->dispatch($pre_sql, $id);
    }

    /**
     * MÉTODO REUTILIZÁVEL: Valida a estrutura da PreSqlDTO.
     * Pode ser chamado de qualquer lugar para apenas validar uma query.
     *
     * @throws \Exception
     */
    public function validate(PreSqlDTO $pre_sql): void
    {
        $this->validate_presql->compare($this->getEntities($pre_sql), $pre_sql);

        $errors = $this->validate_presql->getErrors();
        if (count($errors) > 0) {
            // Lança uma exceção específica com os erros, que é uma prática melhor.
            throw new \Exception(json_encode($this->validate_presql->getErrors()));
        }
    }

    /**
     * Lógica de persistência e disparo do job, agora separada.
     * Este método lida com a criação (se id=null) ou atualização de uma query.
     */
    private function dispatch(PreSqlDTO $pre_sql, ?int $id = null): Querry
    {

        $query = Querry::updateOrCreate(
            ['id' => $id], // Chave para encontrar
            [ // Dados para criar ou atualizar
                'connection_id' => $this->struct_service->getConnection()->id,
                'hash' => $this->generateStableHash($pre_sql),
                'type' => QuerryType::JSON,
                'struct' => $pre_sql->toArray(),
                'status' => QuerryStatusEnum::PENDING,
            ]
        );

        ProcessQuerryBuilder::dispatch($query->id);

        return $query;
    }

    /**
     * Helper para gerar o hash. Permanece o mesmo.
     */
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