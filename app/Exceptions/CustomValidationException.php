<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CustomValidationException extends Exception
{
    /**
     * CustomValidationException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Something was wrong', Throwable $previous = null)
    {
        parent::__construct($message, 422, $previous);
    }
}
