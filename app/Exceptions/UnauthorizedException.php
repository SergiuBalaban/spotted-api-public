<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Throwable;

class UnauthorizedException extends AuthenticationException
{
    /**
     * UnauthorizedException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Unauthorized', Throwable $previous = null)
    {
        parent::__construct($message, [], $previous);
    }
}
