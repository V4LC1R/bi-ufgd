<?
namespace App\Modules\Querry\Jobs;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Services\BuildQuerryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessQuerryBuilder implements ShouldQueue
{
     use Dispatchable, Queueable;

    public function __construct(
        protected int $querry_id
    ) {}

    public function handle(BuildQuerryService $builder)
    {
        $pre_sql = Querry::findOrFail($this->querry_id);

        if(!$pre_sql)
            throw new \Exception("Querry not found!");

        $dto = new PreSqlDTO($pre_sql->struct);

        $builder->makeQuerry($dto);
    }
}
