<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ForbiddenException extends Exception
{
    public function __construct(string $message = 'Action not allowed!', ?Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN, $previous);
    }
}
