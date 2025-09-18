<?
namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Querry\Constants\QuerryType;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Jobs\ProcessQuerryBuilder;
use App\Modules\Querry\Models\Querry;
use App\Modules\Querry\Services\ValidatePreSqlService;

class QuerryService 
{

    public function __construct(
        protected IStructTable $struct_service,
        protected ValidatePreSqlService $validate_presql
    ) {}

    public function savePreSql(PreSqlDTO $pre_sql)
    {
        $this->struct_service->setConnectionName($pre_sql->connectionName);

        $roles = $this->struct_service->getStructConnection();

        $this->validate_presql->compare($roles,$pre_sql);

        if(count($this->validate_presql->getErrors()) > 0)
            throw new \Exception(json_encode($this->validate_presql->getErrors()));

        $query = Querry::create([
            'connection_id'=> $this->struct_service->getConnection()->id,
            'hash' => hash('sha256', json_encode($pre_sql->fact)),
            'type'=> QuerryType::JSON,
            'struct'=> json_encode($pre_sql->toArray()),
        ]);

        ProcessQuerryBuilder::dispatch($query->id);

    }
}