<?
namespace App\Modules\Connection\Services;
use App\Modules\Connection\Builder\DinamicConnectionBuilder;
use App\Modules\Connection\Contracts\StructTable;
use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Connection\Models\Connection;
use App\Modules\Connection\Models\Tables;
use Illuminate\Support\Facades\DB;

class StructTableService implements StructTable
{
    protected ?string $connectionName = null;

    /** @var TableDTO[] */
    protected array $tables = [];
    protected array $roles = [];
    protected $connection = null;

    public function setConnectionName(string $connectionName): self
    {
        //poder resetar a lista de tabelas
        $this->tables = [];
        $this->connection = null;
        $this->connectionName = $connectionName;

        return $this;
    }

    /**
     * Retorna colunas de uma tabela
     * @param string $table
     * @return array ['id' => 'int:pk', 'name' => 'varchar:255']
     */
    public function getColumns(string $table): array
    {
        return $this->getTables()[$table]->columns ?? [];
    }

    public function getConnection()
    {

        if ($this->connection)
            return $this->connection;

        if (!$this->connection) {
            $this->connection = Connection::where('name', $this->connectionName)
                ->first();
        }

        if (!$this->connection)
            throw new \Exception("Connection not found!");

        return $this->connection;
    }


    public function getDriver(): string
    {
        return $this->getConnection()->type ?? '';
    }

    /**
     * Retorna as tabelas e suas estruturas
     *
     * @return array<string,TableDTO>
     */
    public function getTables(): array
    {
        if (!$this->connectionName)
            throw new \Exception("Connection name not found!");

        if (count($this->tables) > 0)
            return $this->tables;

        $this->getConnection();

        $tables = Tables::select(['name', 'columns', 'type'])
            ->where('connection_id', $this->connection->id)
            ->get();

        foreach ($tables as $table) {
            $this->tables[$table->name] = new TableDTO($table->toArray());
        }

        return $this->tables;
    }

    /**
     * Summary of getStructConnection
     * @return array<string, array<string,TableDTO[]>> $roles
     */
    public function getStructConnection(): array
    {
        $this->roles = [];
        foreach ($this->getTables() as $table) {
            $this->roles[$table->type][$table->name] = $table;
        }

        return $this->roles;
    }

    public function getTablesNames(): array
    {
        if (!$this->connectionName)
            throw new \Exception("Connection name not found!");

        foreach ($this->getTables() as $table) {
            $this->roles[$table->type][] = $table->name;
        }

        return $this->roles;
    }

    /**
     * Retorna relacionamentos (foreign keys)
     * @param string $table
     * @return array ['id_first' => 'fk:First.id']
     */
    public function getRelations(): array
    {
        $tables = $this->getTables();
        $relations = [];

        foreach ($tables as $table_name => $table) {
            foreach ($table->columns as $col => $def) {

                // A definição é algo como: "number:fk:TabelaDestino.ColunaDestino"
                // Vamos quebrar a string pelo caractere ':'
                $parts = explode(':', $def);

                // A parte que nos interessa é a terceira (índice 2),
                // que contém a relação. Fazemos uma verificação de segurança.
                if (!isset($parts[1]) && $parts[1] !== 'fk' && !isset($parts[2]))
                    continue;

            }
        }

        return $relations;
    }

    public function getFactStruct(string $connection_name): array
    {
        $list = $this
            ->setConnectionName($connection_name)
            ->getStructConnection()['fact'];

        $fact_name = array_keys($list)[0];
        return array_keys($list[$fact_name]->columns);
    }

}