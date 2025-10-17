<?php
namespace App\Modules\Connection\Services;

use App\Modules\Connection\Contracts\DynamicConnectionManager;

use App\Modules\Connection\Errors\CacheQueryMissingError;
use App\Modules\Connection\Errors\QueryExecutionException;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Contracts\QueryExecutor;
use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Models\Querry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecuteSqlService implements QueryExecutor
{
    public function __construct(
        protected DynamicConnectionManager $conn_manager
    ) {
    }

    public function executeAndCache(Querry $query)
    {
        try {

            $result = $this->execute($query->connection, $query);

            $query->status = QuerryStatusEnum::SUCCESS;

            $this->cache($query, $result);

            return $result;

        } catch (\Throwable $th) {
            $query->status = QuerryStatusEnum::FAIL;
            $query->error_message = mb_substr($th->getMessage(), 0, 500);
            if ($th instanceof QueryExecutionException) {
                throw $th;
            }
            // Se for um erro do DB (PDOException), o "embrulhamos" com o nosso
            throw QueryExecutionException::genericFailure($query, $th);
        } finally {
            $query->save();
        }

    }

    private function execute(Connection $conn, Querry $query)
    {
        if (!$query->literal_query) {
            throw new CacheQueryMissingError("Querry cache don't find!");
        }

        if (empty($query->literal_query)) {
            throw new QueryExecutionException("O SQL para a Query ID {$query->id} não foi gerado ou não foi encontrado.");
        }

        return DB::connection($this->conn_manager->setup($conn))
            ->select($query->literal_query, json_decode($query->binds) ?? []);
    }

    private function cache(Querry $query, $result)
    {
        $cacheKey = "query_result_{$query->hash}";
        $connectionTag = "connection_{$query->connection_id}";

        Cache::tags([$connectionTag])
            ->put(
                $cacheKey,
                $result,
                now()->addHours(24)
            );
    }
}