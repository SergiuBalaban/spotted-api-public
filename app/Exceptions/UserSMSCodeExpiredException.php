<?php

namespace App\Exceptions;

use App\Exceptions\CustomMessages\ErrorMessageValue;

class UserSMSCodeExpiredException extends ForbiddenException
{
    public function __construct()
    {
        parent::__construct(json_encode([
            'message' => ErrorMessageValue::ERROR_SMS_EXPIRED_MESSAGE,
            'code' => ErrorMessageValue::ERROR_SMS_EXPIRED_CODE,
        ], JSON_THROW_ON_ERROR));
    }
}
