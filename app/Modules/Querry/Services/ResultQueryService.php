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
        $cacheKey = "query_result_{$hash}";

        // Tenta pegar do cache
        $cachedResult = Cache::get($cacheKey);

        if ($cachedResult !== null) {
            return $cachedResult; // Cache "hit"
        }

        // Cache "miss": chama o método privado para fazer o trabalho pesado.
        return $this->findAndExecuteQuery($hash);
    }

    /**
     * Método privado que encontra a query no banco e a executa.
     * @throws ModelNotFoundException se a query não for encontrada.
     */
    private function findAndExecuteQuery(string $hash): array
    {
        // 1. BUSCA A QUERY (ou falha com uma exceção clara)
        $query = Querry::where('hash', $hash)
            ->where('status', QuerryStatusEnum::SUCCESS)
            ->firstOrFail();

        return $this->query_excutor->executeAndCache($query);
    }
}