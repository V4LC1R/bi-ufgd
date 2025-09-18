<?
namespace App\Modules\Connection\Contracts;

interface IStructTable
{
    /**
     * Seleciona a conexão pelo nome cadastrado
     * Ex: "graduacao", "financeiro"
     */
    public function setConnectionName(string $connectionName): self;
    
     /**
     * Retorna lista de tabelas disponíveis no banco.
     *
     * @return array ['First', 'Main', ...]
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
    public function getRelations(string $table): array;

    public function getStructConnection(): array;

    public function getConnection();

    public function getDriver():string;

}