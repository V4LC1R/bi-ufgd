<?
namespace App\Modules\Connection\Http\DTOs;

class TableDTO
{
    public string $name;
    public string $alias;
    public array $columns; // struct
    public array $relations;

    public string $type;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? '';
        $this->alias = $data['alias'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->type = $data['type'];
    }
}
