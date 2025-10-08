<?php

namespace App\Modules\Connection\Jobs;

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
        protected string $query_id,
        protected int $connection_id
    ) {
        // Define a fila diretamente no trait
        $this->onQueue('simple_querry_exec');
    }

    public function handle(ExecuteSqlService $executor)
    {

        try {
            $conn =  Connection::findOrFail($this->connection_id);

            $query = Querry::findOrFail($this->query_id);

            $executor->executeSqlCached($conn,$query->hash);

        }catch(CacheQueryMissingError $e){
        
                

        }catch (\Throwable $th) {
        


        }
    }
}
