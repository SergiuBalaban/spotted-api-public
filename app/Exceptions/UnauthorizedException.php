<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Throwable;

class UnauthorizedException extends AuthenticationException
{
    public function __construct(string $message = 'Unauthorized', ?Throwable $previous = null)
    {
        parent::__construct($message, [], $previous);
    }
}
