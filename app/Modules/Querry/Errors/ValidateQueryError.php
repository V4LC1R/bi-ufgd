<?php

namespace App\Modules\Querry\Errors;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Exceção lançada quando a validação de negócio do PreSqlDTO falha,
 * antes mesmo de a query ser enfileirada para construção.
 */
class ValidateQueryError extends RuntimeException
{
    /**
     * @var array Um array associativo com os erros de validação.
     * Ex: ['dimensions.0.table' => 'A tabela "Dim_Cliente" não existe.']
     */
    protected array $errors = [];

    /**
     * O construtor é protegido para forçar o uso do factory method, garantindo
     * que a exceção sempre carregue um payload de erros estruturado.
     */
    protected function __construct(string $message = "Os dados fornecidos são inválidos.", int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Método de fábrica principal para criar a exceção com um conjunto de erros.
     *
     * @param array $errors Array associativo de erros de validação.
     * @return self
     */
    public static function withErrors(array $errors): self
    {
        $instance = new self();
        $instance->errors = $errors;
        return $instance;
    }

    /**
     * Registra a tentativa de consulta inválida no log.
     * Usamos 'warning' porque é um erro do cliente, não uma falha do servidor.
     */
    public function report(): void
    {
        Log::warning('Falha na validação da Pre-Query:', [
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ]);
    }

    /**
     * Transforma a exceção em uma resposta de API HTTP 422,
     * seguindo o padrão de respostas de validação do Laravel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->getCode());
    }
}