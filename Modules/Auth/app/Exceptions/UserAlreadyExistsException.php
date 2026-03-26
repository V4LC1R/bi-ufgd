<?php

namespace Modules\Auth\Exceptions;

use Exception;

class UserAlreadyExistsException extends Exception
{
    public function __construct(
        string $message = 'User already exists',
        int $code = 409
    ) {
        parent::__construct($message, $code);
    }
}