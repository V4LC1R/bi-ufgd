<?php

namespace App\Modules\Query\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Exceção lançada especificamente quando ocorre um erro na fase de construção
 * da consulta SQL, antes de sua execução.
 */
class BuildQueryError extends RuntimeException
{
    // Adicionamos uma propriedade para guardar contexto extra para o log.
    protected array $context = [];

    /**
     * O construtor agora é protegido para encorajar o uso dos factory methods,
     * garantindo que todas as exceções deste tipo sejam criadas de forma consistente.
     */
    protected function __construct(string $message = "", int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Método de fábrica para erros de lógica na estrutura da query.
     * Ex: Uma subdimensão foi pedida sem sua dimensão-pai.
     *
     * @param string $reason A razão específica da falha.
     * @param array $context Informação extra para debug (ex: ['query_id' => 123]).
     * @return self
     */
    public static function logicalError(string $reason, array $context = []): self
    {
        // Usamos 422 para indicar um erro na "entidade" (a query) enviada pelo usuário.
        $instance = new self($reason, 422);
        $instance->context = $context;
        return $instance;
    }

    /**
     * Método de fábrica para erros inesperados durante o processo de build.
     * Ex: Uma classe de Builder não foi encontrada ou um erro interno ocorreu.
     *
     * @param array $context Informação extra para debug.
     * @param Throwable $previous A exceção original que causou a falha.
     * @return self
     */
    public static function unexpected(array $context = [], ?Throwable $previous = null): self
    {
        $message = 'Um erro inesperado ocorreu durante a construção da consulta SQL.';

        $instance = new self($message, 500, $previous);
        $instance->context = $context;
        return $instance;
    }

    /**
     * Registra a exceção no sistema de logs.
     * O Laravel chama este método automaticamente quando a exceção não é capturada.
     */
    public function report(): void
    {
        Log::error($this->getMessage(), $this->context);
    }

    /**
     * Transforma a exceção em uma resposta HTTP JSON.
     * O Laravel chama este método automaticamente se a exceção chegar à camada HTTP.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'build_failed',
            'message' => $this->getMessage(),
            // Em ambiente de desenvolvimento, podemos adicionar mais detalhes
            'details' => config('app.debug') ? $this->getTraceAsString() : 'Ocorreu um erro na construção da consulta.',
        ], $this->getCode());
    }
}