<?php
namespace App\Modules\Connection\Http\DTOs;

class ConnectionDTO
{
    public string $name;
    public string $host;
    public string $port;
    public string $password;
    public string $user;
    public string $type;
    public string $database;


    /** @var TableDTO[] */
    public array $tables = [];

    public function __construct(array $data)
    {
        $connection = $data["connection"] ?? [];

        $this->name = $data['name'] ?? '';
        $this->host = $connection['host'] ?? '';
        $this->password = $connection['password'] ?? $connection['passoword'] ?? ''; // aceita typo também
        $this->port = $connection['port'] ?? '';
        $this->type = $connection['type'] ?? '';
        $this->user = $connection["user"] ?? '';
        $this->database = $connection["database"] ?? '';

        $this->tablesFill($data['tables'] ?? []);
    }

    private function tablesFill(array $data): void
    {
        foreach ($data as $table) {
            $this->tables[] = new TableDTO($table);
        }
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'host' => $this->host,
            'user' => $this->user,
            'password' => $this->password,
            'database' => $this->database,
            'type' => $this->type,
        ];
    }
}
