<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomValidationException extends Exception
{
    public function __construct(string $message = 'Something was wrong', ?Throwable $previous = null)
    {
        parent::__construct($message, 422, $previous);
    }
}
