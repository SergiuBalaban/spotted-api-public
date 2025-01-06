<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends Exception
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_NOT_FOUND;

    /**
     * @var string
     */
    protected $message = 'Resource not found.';
}
