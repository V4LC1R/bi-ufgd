<?php

namespace App\Modules\Connection\Jobs;

use App\Modules\Connection\Contracts\QueryExecutor;
use App\Modules\Connection\Errors\CacheQueryMissingError;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Services\ExecuteSqlService;
use App\Modules\Querry\Models\Querry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessQuerryExecute implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        protected string $query_id
    ) {
        // Define a fila diretamente no trait
        $this->onQueue('exec');
    }

    public function handle(QueryExecutor $executor)
    {

        try {
            $query = Querry::findOrFail($this->query_id);

            $executor->executeAndCache($query);

        } catch (CacheQueryMissingError $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
