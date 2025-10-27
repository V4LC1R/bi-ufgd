<?
namespace App\Modules\Connection\Services;


use App\Modules\Connection\Http\DTOs\ConnectionDTO;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Models\Tables;

use App\Modules\Querry\Jobs\ProcessRevalidatePreSql;
use App\Modules\Querry\Services\QuerryService;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConnectionService
{
    public function __construct(
        protected QuerryService $queryService
    ) {
    }

    public function getList()
    {
        return Connection::select(['id', 'name'])->get();
    }

    public function create(ConnectionDTO $dto)
    {
        try {
            DB::beginTransaction();

            $connection = Connection::create($dto->toArray());

            $tables = [];

            foreach ($dto->tables as $table) {
                $tables[] = [
                    'connection_id' => $connection->id,
                    'name' => $table->name,
                    'alias' => $table->alias,
                    'columns' => json_encode($table->columns),
                    'type' => $table->type
                ];
            }
            Tables::insert($tables);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception($th->getMessage());
        }
    }

    public function edit(ConnectionDTO $dto, $conn_id)
    {
        try {
            $this->updateConnectionAndRecreateTables($dto, $conn_id);

            //preciso invalidar todo o cache - aqui
            Cache::tags("connection_{$conn_id}")->flush();

            ProcessRevalidatePreSql::dispatch($conn_id);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateConnectionAndRecreateTables(ConnectionDTO $dto, $conn_id)
    {
        try {
            DB::beginTransaction();

            $connection = Connection::findOrFail($conn_id);

            if (!$connection) {
                throw new \Exception("Connection dont find");
            }

            $connection->update($dto->toArray());
            $connection->tables()->delete();

            $newTablesData = [];
            foreach ($dto->tables as $tableDto) {
                $newTablesData[] = array_merge(
                    $tableDto->toArray(),
                    [
                        'connection_id' => $connection->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            Tables::insert($newTablesData);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

}