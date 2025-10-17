<?php

namespace App\Modules\Connection\Errors;


use App\Modules\Querry\Models\Querry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Exceção lançada especificamente quando ocorre um erro durante a fase de execução
 * da consulta SQL no banco de dados de destino.
 */
class QueryExecutionException extends RuntimeException
{
    /**
     * @var array Contexto adicional para ser incluído nos logs.
     */
    protected array $context = [];

    /**
     * Construtor protegido para forçar o uso dos métodos de fábrica.
     */
    protected function __construct(string $message = "", int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Método de fábrica para quando o pré-requisito da execução (o SQL gerado) não é encontrado.
     *
     * @param Querry $query O modelo da query que falhou.
     * @return self
     */
    public static function missingSql(Querry $query): self
    {
        $message = "O SQL para a Query ID {$query->id} não foi gerado ou não foi encontrado no banco de dados.";

        $instance = new self($message);
        $instance->context = ['query_id' => $query->id, 'hash' => $query->hash];

        return $instance;
    }

    /**
     * Método de fábrica para "embrulhar" uma falha genérica ocorrida durante a execução no banco de dados.
     *
     * @param Querry $query O modelo da query que falhou.
     * @param Throwable $previous A exceção original (ex: PDOException) que causou o erro.
     * @return self
     */
    public static function genericFailure(Querry $query, Throwable $previous): self
    {
        $message = "Falha ao executar a Query ID {$query->id}: " . $previous->getMessage();

        // Usa o código da exceção original, se disponível, ou 500 como padrão.
        $code = $previous->getCode() ? (int) $previous->getCode() : 500;

        $instance = new self($message, $code, $previous);
        $instance->context = ['query_id' => $query->id, 'hash' => $query->hash];

        return $instance;
    }

    /**
     * Registra a exceção e seu contexto no sistema de logs.
     */
    public function report(): void
    {
        Log::error($this->getMessage(), $this->context);

        // Se houver uma exceção original, loga o stack trace dela para depuração completa.
        if ($this->getPrevious()) {
            Log::error('Original Exception Trace:', ['exception' => $this->getPrevious()]);
        }
    }

    /**
     * Transforma a exceção em uma resposta HTTP JSON.
     * Embora menos provável de ser chamada em um job, é uma boa prática implementá-la.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        // Erros de execução são quase sempre erros do servidor (500).
        $statusCode = 500;

        return response()->json([
            'status' => 'execution_failed',
            'message' => 'Ocorreu um erro inesperado ao processar sua consulta no banco de dados.',
            // Em ambiente de desenvolvimento, podemos mostrar o erro real.
            'details' => config('app.debug') ? $this->getMessage() : null,
        ], $statusCode);
    }
}