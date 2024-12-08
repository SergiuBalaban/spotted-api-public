<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ForbiddenException extends Exception
{
    /**
     * CustomValidationException constructor.
     *
     * CustomException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Action not allowed!', Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN, $previous);
    }
}
