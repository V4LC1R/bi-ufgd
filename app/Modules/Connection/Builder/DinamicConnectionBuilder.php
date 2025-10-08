<?php

namespace App\Modules\Connection\Builder;

use App\Modules\Connection\Models\Connection;

class DinamicConnectionBuilder
{
    private array $config = [];

    public function __construct(string $driver)
    {
        $this->config['driver'] = $driver;
        // Preenche com alguns padrões do Laravel
        $this->config['charset'] = 'utf8mb4';
        $this->config['collation'] = 'utf8mb4_unicode_ci';
        $this->config['prefix'] = '';
    }

    public function setHost(string $host): self
    {
        $this->config['host'] = $host;
        return $this;
    }

    public function setPort(?int $port): self
    {
        $this->config['port'] = $port ?? $this->getDefaultPort($this->config['driver']);
        return $this;
    }

    public function setDatabase(string $database): self
    {
        $this->config['database'] = $database;
        return $this;
    }

    public function setUser(string $user): self
    {
        $this->config['username'] = $user;
        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->config['password'] = $password;
        return $this;
    }

    /**
     * Retorna o array de configuração final.
     */
    public function build(): array
    {
        return $this->config;
    }

    /**
     * Método "Factory" para construir a configuração diretamente a partir do modelo Eloquent.
     */
    public static function fromModel(Connection $model): array
    {
        return (new self($model->type))
            ->setHost($model->host)
            ->setDatabase($model->database)
            ->setUser($model->user)
            ->setPassword($model->password)
            // Assumindo que você possa ter uma coluna 'port' no seu modelo
            ->setPort($model->port ?? null)
            ->build();
    }

    private function getDefaultPort(string $driver): ?int
    {
        return match ($driver) {
            'mysql' => 3306,
            'pgsql' => 5432,
            'sqlsrv' => 1433,
            default => null,
        };
    }
}