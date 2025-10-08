<?php
namespace App\Modules\Connection\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Errors\CacheQueryMissingError;
use App\Modules\Connection\Models\Connection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecuteSqlService
{

    public function __construct(
        protected ConnectionService $connection_service,
        protected IStructTable $struct
    ){}
    public function executeAndCache(Connection $conn, $hash,$dump=false)
    {
        try {
           
            $sql_data = $this->getFromCache($hash);

            if(!$sql_data['bind'] || !isset($sql_data['bind'])){
              throw new CacheQueryMissingError("Querry cache don't find!");
            }
            
            $conn_name =  $this->struct
                ->setConnectionName($conn->name)
                ->swapToDataBase();

            $result = DB::connection($conn_name)->select($sql_data['sql'],$sql_data['bind']);

            $cacheKey = "query_result_{$hash}";
            Cache::put($cacheKey, $result, now()->addHours(24));

            if($dump)
                return $result;

        } catch (\Throwable $th) {
            dd($th);
        }
    }


    private function getFromCache(string $hash)
    {
        return [
            'sql' =>Cache::get("$hash-sql"),
            'bind' =>  Cache::get("$hash-bindings")
        ];
    }
}