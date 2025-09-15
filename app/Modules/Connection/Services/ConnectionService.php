<?
namespace App\Modules\Connection\Services;

use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Models\Tables;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\table;

class ConnectionService
{
    public function create(ConnectionDTO $dto)
    {
        try {
            DB::beginTransaction();

                $connection = Connection::create([
                    'name'=>$dto->name,
                    'host'=>$dto->host,
                    'user'=>$dto->user,
                    'password'=>$dto->password,
                    'database'=>$dto->database,
                    'type'=>$dto->type,
                ]);

                $tables = [];

                foreach ($dto->tables as $table) {
                    $tables[] = [
                        'connection_id'=>$connection->id,
                        'name'=>$table->name,
                        'alias'=>$table->alias, 
                        'struct'=>json_encode($table->columns),
                        'type'=>$table->type
                    ];
                }
                
               Tables::insert($tables);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception($th->getMessage());
        }
    }
}