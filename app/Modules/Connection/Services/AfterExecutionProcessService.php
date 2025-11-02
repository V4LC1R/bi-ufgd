<?php // Adicionado o tag de abertura do PHP

namespace App\Modules\Connection\Services;

use App\Modules\Connection\Contracts\FieldRelationResult;
use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\SubDimensionDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;


// Corrigido typo: Realtion -> Relation
class AfterExecutionProcessService implements FieldRelationResult
{

    public function setup(PreSqlDTO $pre_sql): array
    {
        // (Assumindo que PreSqlDTO tem estas propriedades)
        return [
            'fact' => $this->factColumns($pre_sql->fact),
            // Corrigido typo: Coluns -> Columns
            'dimensions' => $this->dimensionColumns($pre_sql->dimensions),
            // Corrigido typo: Coluns -> Columns
            'sub-dimensions' => $this->subDimensionColumns($pre_sql->subDimensions)
        ];
    }

    /**
     * Extrai colunas de FactDTO, usando o alias do agregado.
     * @param FactDTO $fact
     * @return array
     */
    public function factColumns(FactDTO $fact): array
    {
        $cols = [];

        // Renomeado $col_name para $columnName para consistência
        foreach ($fact->columns as $columnName => $actions) {
            $aliasEmpty = empty($actions->alias);

            foreach ($actions->aggregates as $agg) {
                if (!$aliasEmpty && array_key_exists($agg, $actions->alias)) {
                    $cols[] = $actions->alias[$agg];
                    continue;
                }
                $cols[] = $columnName; // Nome da coluna base
            }
        }

        return $cols;
    }

    /**
     * Extrai colunas de DimensionDTOs.
     * @param DimensionDTO[] $dimensions
     * @return array
     */
    public function dimensionColumns(array $dimensions): array // Corrigido typo
    {
        // Chama o helper privado
        return $this->extractColumnsWithAlias($dimensions);
    }

    /**
     * Extrai colunas de SubDimensionDTOs.
     * @param SubDimensionDTO[] $dimensions
     * @return array
     */
    public function subDimensionColumns(array $dimensions): array // Corrigido typo
    {
        // Chama o mesmo helper privado
        return $this->extractColumnsWithAlias($dimensions);
    }


    /**
     * MÉTODO PRIVADO REUTILIZADO (NOVO)
     * * Lógica genérica para extrair colunas de DTOs baseados em dimensão
     * que possuem as propriedades ->columns e ->alias.
     *
     * @param (DimensionDTO|SubDimensionDTO)[] $dimensionItems
     * @return array
     */
    private function extractColumnsWithAlias(array $dimensionItems): array
    {
        $cols = [];

        foreach ($dimensionItems as $dim) {

            // Simplificado: empty() já cobre o caso de ser nulo ou um array vazio.
            if (empty($dim->columns)) {
                continue;
            }

            // Simplificado: empty() também é suficiente aqui.
            $aliasEmpty = empty($dim->alias);

            // Renomeado $realated_col para $columnName para clareza
            foreach ($dim->columns as $columnName) {

                if (!$aliasEmpty && array_key_exists($columnName, $dim->alias)) {
                    $cols[] = $dim->alias[$columnName];
                    continue;
                }

                $cols[] = $columnName;
            }
        }

        return $cols;
    }
}