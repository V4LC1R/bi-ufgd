<?php
namespace App\Modules\Connection\Services;

use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Connection\Errors\CacheQueryMissingError;
use App\Modules\Connection\Errors\QueryExecutionException;
use App\Modules\Connection\Models\Connection;
use App\Modules\Querry\Models\Querry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecuteSqlService
{
    public function __construct(
        protected ConnectionService $connection_service,
        protected StructTable $struct,
        protected DynamicConnectionManager $conn_manager
    ) {
    }
    public function executeAndCache(Connection $conn, Querry $query, $dump = false)
    {
        try {

            if (!$query->literal_query) {
                throw new CacheQueryMissingError("Querry cache don't find!");
            }

            if (empty($query->literal_query)) {
                throw new QueryExecutionException("O SQL para a Query ID {$query->id} não foi gerado ou não foi encontrado.");
            }

            $result = DB::connection($this->conn_manager->setup($conn))
                ->select($query->literal_query, json_decode($query->binds) ?? []);

            $cacheKey = "query_result_{$query->hash}";
            $connectionTag = "connection_{$query->connection_id}";
            Cache::tags([$connectionTag])
                ->put(
                    $cacheKey,
                    $result,
                    now()->addHours(24)
                );

            if ($dump)
                return $result;

        } catch (\Throwable $th) {

            if ($th instanceof QueryExecutionException) {
                throw $th;
            }

            // Se for um erro do DB (PDOException), o "embrulhamos" com o nosso
            throw QueryExecutionException::genericFailure($query, $th);
        }
    }
}