<?
namespace App\Modules\Connection\Services;
use App\Modules\Connection\Contracts\StructTableContract;
use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Models\Tables;

class StructTableService implements StructTableContract
{
    protected ?string $connectionName = null;
    
    /** @var TableDTO[] */
    protected array $tables = [];
    protected array $roles = [];

    public function setConnectionName(string $connectionName): void
    {
        //poder resetar a lista de tabelas
        $this->tables = [];
        $this->connectionName = $connectionName;
    }
    
     /**
     * Retorna as tabelas e suas estruturas
     *
     * @return array ['First', 'Main', ...]
     */
    public function getTables(): array 
    {
        if(!$this->connectionName)
            throw new \Exception("Connection name not found!");

        if(count($this->tables) > 0)
            return $this->tables;

         $connection = Connection::select('id')
            ->where('name', $this->connectionName)
            ->first();

        if (!$connection) {
            return [];
        }

        $tables = Tables::select(['name', 'alias', 'struct','type'])
            ->where('connection_id', $connection->id)
            ->get();

        foreach ($tables as $table) {
            $this->tables[$table->name] = new TableDTO($table->toArray());
        }

        return $this->tables;
    }

    public function getStructConnection():array
    {
        $this->roles = [];
        foreach ($this->getTables() as $table) {
            $this->roles[$table->type][$table->name] = $table;
        }

        return $this->roles;
    }

    /**
     * Retorna colunas de uma tabela
     * @param string $table
     * @return array ['id' => 'int:pk', 'name' => 'varchar:255']
     */
    public function getColumns(string $table): array
    {
        $tables = $this->getTables();

        return $tables[$table]->columns ?? [];
    }

    /**
     * Retorna relacionamentos (foreign keys)
     * @param string $table
     * @return array ['id_first' => 'fk:First.id']
     */
     public function getRelations(string $table): array
    {
        $columns = $this->getColumns($table);
        $relations = [];

        foreach ($columns as $colName => $colStruct) {
            if (isset($colStruct['fk']) && !empty($colStruct['fk'])) {
                $relations[$colName] = $colStruct['fk'];
            }
        }

        return $relations;
    }
}