<?php

namespace App\Modules\Connection\Services;

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Http\DTOs\DimensionFilterDTO;
use App\Modules\Connection\Http\DTOs\FilterCriterioDTO;
use App\Modules\Connection\Models\Tables;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DimensionDataService
{
    /**
     * O serviço depende do contrato do gerenciador de conexões dinâmicas,
     * mantendo-o desacoplado da implementação concreta.
     */
    public function __construct(
        protected DynamicConnectionManager $connectionManager
    ) {
    }

    /**
     * Ponto de entrada principal do serviço. Orquestra a busca de dados.
     *
     * @param Tables $table O modelo Eloquent que descreve a tabela de dimensão.
     * @param DimensionFilterDTO $dto O objeto de transferência de dados com todos os filtros,
     * ordenação e paginação já validados.
     * @return LengthAwarePaginator Um objeto de paginação do Laravel.
     */
    public function getFilteredPaginatedData(Tables $table, DimensionFilterDTO $dto): LengthAwarePaginator
    {

        dd('oci');

        // 1. Prepara o ambiente: ativa a conexão com o Data Warehouse correto.
        $connectionName = $this->connectionManager->setup($table->connection);

        // 2. Inicia o Query Builder na conexão e tabela corretas.
        $query = DB::connection($connectionName)->table($table->name);

        // 3. Delega a aplicação dos filtros para um método auxiliar.
        $this->applyFilters($query, $dto->filters);

        // 4. Aplica a ordenação usando os dados do DTO.
        $query->orderBy($dto->sortBy, $dto->sortDirection);

        // 5. Executa a query com paginação e retorna o resultado.
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }

    /**
     * Método privado responsável por aplicar a lógica de filtragem.
     *
     * @param Builder $query A instância do Query Builder a ser modificada.
     * @param FilterCriterioDTO[] $filters A lista de critérios de filtro.
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            // Delega a tradução do operador para outro método auxiliar.
            [$sqlOperator, $sqlValue] = $this->translateFilterCriterion($filter);

            // Aplica a cláusula WHERE ao builder.
            $query->where($filter->column, $sqlOperator, $sqlValue);
        }
    }

    /**
     * Traduz um único critério de filtro da API para a sintaxe SQL.
     *
     * @param FilterCriterioDTO $filter O objeto DTO do critério.
     * @return array Um array contendo o operador SQL e o valor formatado.
     */
    private function translateFilterCriterion(FilterCriterioDTO $filter): array
    {
        return match ($filter->operator) {
            'eq' => ['=', $filter->value],
            'gt' => ['>', $filter->value],
            'lt' => ['<', $filter->value],
            'LIKE' => ['LIKE', $filter->value],
            'startWith' => ['LIKE', "{$filter->value}%"],
            'endWith' => ['LIKE', "%{$filter->value}"],
            'contains' => ['LIKE', "%{$filter->value}%"],
            default => throw new InvalidArgumentException("Operador de filtro desconhecido: {$filter->operator}"),
        };
    }
}