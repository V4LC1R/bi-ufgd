<?php
namespace App\Modules\Querry\Jobs;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Services\BuildQuerryService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessQuerryBuilder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        protected int $querry_id
    ) {
        // Define a fila diretamente no trait
        $this->onQueue('simple_querry');
    }

    public function handle(BuildQuerryService $builder)
    {
        $pre_sql = Querry::find($this->querry_id);

        if (!$pre_sql) {
            \Log::error("Querry ID {$this->querry_id} não encontrado!");
            return;
        }
         \Log::info("Processando Querry ID: {$this->$pre_sql->struct}");
         
        $dto = new PreSqlDTO($pre_sql->struct);

        $builder->makeQuerry($dto,$pre_sql);

        \Log::info("Querry ID {$this->querry_id} processada com sucesso!");
    }
}
