<?php
namespace App\Modules\Querry\Services;
use App\Modules\Connection\Contracts\QueryExecutor;

use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Models\Querry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class ResultQueryService
{

    public function __construct(
        protected QueryExecutor $query_excutor
    ) {

    }
    public function getResultByHash(string $hash): array
    {
        $query = Querry::where('hash', $hash)
            ->where('status', QuerryStatusEnum::SUCCESS)
            ->firstOrFail();

        $cacheKey = "query_result_{$hash}";
        $connectionTag = "connection_{$query->connection_id}";

        // 3. TENTA PEGAR DO CACHE (AGORA USANDO A TAG CORRETA)
        $cachedResult = Cache::tags([$connectionTag])->get($cacheKey);

        if ($cachedResult !== null) {
            return [
                'fields' => array_keys($cachedResult[0]),
                'data' => $cachedResult,
                'by' => 'RD'
            ]; // Cache "hit"
        }

        // Cache "miss": chama o método privado para fazer o trabalho pesado.
        return $this->findAndExecuteQuery($query);
    }

    /**
     * Método privado que encontra a query no banco e a executa.
     * @throws ModelNotFoundException se a query não for encontrada.
     */
    private function findAndExecuteQuery(Querry $query): array
    {
        $result = $this->query_excutor->executeAndCache($query);

        if (empty($result)) {
            return [
                'fields' => [], // Sem campos se não há dados
                'data' => []
            ];
        }

        return [
            'fields' => array_keys($result[0]),
            'data' => $result,
            'by' => 'DB'
        ];
    }
}