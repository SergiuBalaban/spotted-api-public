<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateResourceFailedException extends Exception
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_EXPECTATION_FAILED;

    /**
     * @var string
     */
    protected $message = 'Failed to create Resource.';
}
