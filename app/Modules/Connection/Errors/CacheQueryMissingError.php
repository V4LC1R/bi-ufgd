<?php

namespace App\Modules\Connection\Errors;

use RuntimeException; // É uma boa prática estender exceções mais específicas
use Throwable;

class CacheQueryMissingError extends RuntimeException
{
    /**
     * O construtor padrão para criar a exceção.
     *
     * @param string $message A mensagem de erro.
     * @param int $code O código do erro (opcional).
     * @param Throwable|null $previous A exceção anterior, para encadeamento (opcional).
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        // Chama o construtor da classe pai (RuntimeException)
        parent::__construct($message, $code, $previous);
    }

    /**
     * Método de fábrica estático para criar a exceção com uma mensagem padronizada.
     * Isso torna o código que lança a exceção mais limpo.
     *
     * @param string $hash O hash da query que falhou.
     * @return self
     */
    public static function forHash(string $hash): self
    {
        $message = "Falha ao construir a query para o hash: {$hash}. Verifique o DTO e o esquema.";
        
        return new self($message);
    }

    /**
     * Outro exemplo de método de fábrica.
     */
    public static function missingParent(string $child, string $hash): self
    {
        $message = "A dimensão-pai para a subdimensão '{$child}' não foi encontrada na query (hash: {$hash}).";

        return new self($message, 422); // Pode até passar um código HTTP sugerido
    }
}