<?php
namespace App\Modules\Querry\Jobs;

use App\Modules\Connection\Jobs\ProcessQuerryExecute;
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
        protected $querry_id
    ) {
        // Define a fila diretamente no trait
        $this->onQueue('build');
    }

    public function handle(BuildQuerryService $builder)
    {
        $pre_sql = Querry::find($this->querry_id);

        try {

            if (!$pre_sql) {
                \Log::error("Querry ID {$this->querry_id} não encontrado!");
                return;
            }

            $builder->makeQuerry($pre_sql, true);

            \Log::info("Querry ID {$this->querry_id} processada com sucesso!");

            ProcessQuerryExecute::dispatch($this->querry_id);
        } catch (\Throwable $th) {
            if ($pre_sql) {
                $pre_sql->error_message = $th->getMessage();
                $pre_sql->save();
            }

            throw $th;
        }
    }
}
