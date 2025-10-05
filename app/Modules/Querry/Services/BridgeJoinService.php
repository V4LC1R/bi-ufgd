<?php

namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Http\DTOs\SubDimensionDTO;
use App\Modules\Querry\Traits\HasUsedDimensions;

class BridgeJoinService
{
    use HasUsedDimensions;
    /**
     * @var array<string, string> Um mapa de [subdimensao => dimensão_pai] para buscas rápidas.
     */
    private array $parentMap = [];

    /**
     * O serviço precisa do IStructTable para conhecer o esquema completo do banco.
     */
    public function __construct(
        protected IStructTable $struct_service
    ) {}

    /**
     * Analisa o PreSqlDTO e adiciona as dimensões-pai que faltam para garantir
     * que todos os caminhos de JOIN sejam possíveis.
     */
    public function resolve(PreSqlDTO $preSql): PreSqlDTO
    {
        // Passo 0: Pega o esquema completo (o grafo inteiro)
        $this->struct_service->setConnectionName($preSql->connectionName);
        $fullSchema = $this->struct_service->getStructConnection();
        $allDimensionsSchema = $fullSchema['dimension'];

        // Passo 1: Cria um "mapa de rotas inversas" [filho => pai] para navegação rápida
        $parentMap = $this->buildParentMap($allDimensionsSchema);
        //dd($parentMap);
        // Passo 2: Identifica todos os "nós" já presentes na requisição
        $presentDimensions = [];
        foreach ($preSql->dimensions as $dim) {
            $presentDimensions[$dim->table] = true;
        }
        foreach ($preSql->subDimensions as $subDim) {
            $presentDimensions[$subDim->table] = true;
        }
        // Passo 3: Inicia uma "fila de verificação" com todos os nós que o usuário pediu.
        // Vamos checar o pai de cada um deles.
        $checkQueue = array_merge( $preSql->subDimensions);

        // Passo 4: Navega para trás no grafo
        while (!empty($checkQueue)) {
            $currentNode = array_shift($checkQueue);
           
            $parentName = $parentMap[$currentNode->table] ?? null;
            
            // Se o nó atual não tem pai, ou se o pai já está presente, o caminho está completo.
            if (!$parentName || isset($presentDimensions[$parentName])) {
                continue;
            }

            // O pai não estava na lista! Vamos adicioná-lo.
            $parentDto = new DimensionDTO([
                'table' => $parentName,
                'columns' => [], // Adicionado apenas para permitir o JOIN
                'alias' => [],
                'filter' => []
            ]);
           
            // Adiciona o DTO do pai à requisição principal
            $preSql->dimensions[] = $parentDto;
          
            // Marca o pai como "presente" para não adicioná-lo de novo
            $presentDimensions[$parentName] = true;
            // Adiciona o pai na fila, para checarmos se ele tem um "avô"
            $checkQueue[] = $parentDto;
        }
        return $preSql;
    }

     /**
     * (Método auxiliar que já construímos antes)
     * Cria um mapa de referência de todas as relações FK [filho => pai].
     */
    private function buildParentMap(array $allDimensions): array
    {
        $map = [];
        foreach ($allDimensions as $parentName => $parentStruct) {
            foreach ($parentStruct->columns as $columnMetadata) {
                if (preg_match('/fk:([a-zA-Z0-9_]+)\./', $columnMetadata, $matches)) {
                    $childName = $matches[1];
                    $map[$childName] = $parentName;
                }
            }
        }
        return $map;
    }

}