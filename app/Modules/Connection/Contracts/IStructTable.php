<?
namespace App\Modules\Connection\Contracts;

use App\Modules\Connection\Http\DTOs\TableDTO;
interface IStructTable
{
    /**
     * Seleciona a conexão pelo nome cadastrado
     * Ex: "graduacao", "financeiro"
     */
    public function setConnectionName(string $connectionName): self;
    
    /**
     * Retorna as tabelas e suas estruturas
     *
     * @return array<string,TableDTO>
     */
    public function getTables(): array;

    /**
     * Retorna colunas de uma tabela
     * @param string $table
     * @return array ['id' => 'int:pk', 'name' => 'varchar:255']
     */
    public function getColumns(string $table): array;

    /**
     * Retorna relacionamentos (foreign keys)
     * @param string $table
     * @return array ['id_first' => 'fk:First.id']
     */
    public function getRelations(): array;

    /**
     * @return array<string, array<string, TableDTO>>
     */
    public function getStructConnection(): array;

    public function getConnection();

    public function getDriver():string;

}