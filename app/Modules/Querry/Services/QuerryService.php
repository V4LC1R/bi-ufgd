<?

use App\Modules\Connection\Contracts\StructTableContract;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use App\Modules\Querry\Services\ValidatePreSqlService;

class QuerryService 
{

    public function __construct(
        protected StructTableContract $struct_service,
        protected ValidatePreSqlService $validate_presql = new ValidatePreSqlService()
    ) {}

    public function savePreSql(PreSqlDTO $pre_sql)
    {
        $this->struct_service->setConnectionName($pre_sql->database);

        $roles = $this->struct_service->getStructConnection();

        $this->validate_presql->compare($roles,$pre_sql);

        if(count($this->validate_presql->getErrors()) > 0)
            throw new \Exception("This Pre-SQL is not compatible with Connection Config");

    }
}