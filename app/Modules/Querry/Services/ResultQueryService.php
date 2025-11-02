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

        $cachedResult = Cache::tags([$connectionTag])->get($cacheKey);

        if ($cachedResult !== null) {
            return $cachedResult; // Cache "hit"
        }

        return $this->query_excutor->executeAndCache($query);
    }

}