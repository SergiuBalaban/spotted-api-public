<?php

namespace App\Exceptions\CustomMessages;

use App\Exceptions\ForbiddenException;

abstract class AbstractForbiddenException extends ForbiddenException
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var int|string
     */
    protected $code;

    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        parent::__construct(json_encode(['message' => $this->message, 'code' => $this->code], JSON_THROW_ON_ERROR));
    }
}
