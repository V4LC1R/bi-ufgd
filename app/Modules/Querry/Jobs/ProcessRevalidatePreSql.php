<?php
namespace App\Modules\Querry\Jobs;

use App\Modules\Querry\Constants\QuerryStatusEnum;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Models\Querry;

use App\Modules\Querry\Services\QuerryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRevalidatePreSql implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected $connection_id
    ) {
        $this->onQueue('validate');
    }

    public function handle(QuerryService $queryService)
    {
        try {
            Querry::where('connection_id', $this->connection_id)
                ->chunkById(100, function ($queries) use ($queryService) {
                    foreach ($queries as $query) {
                        try {
                            $dto = new PreSqlDTO($query->struct);
                            $queryService->savePreSql($dto, $query->id);

                        } catch (\Throwable $th) {
                            $query->status = QuerryStatusEnum::INVALID;
                            $query->error_message = $th->getMessage();
                            $query->save();
                        }
                    }
                });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
