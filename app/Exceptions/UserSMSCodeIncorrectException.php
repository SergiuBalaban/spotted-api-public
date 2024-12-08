<?php

namespace App\Exceptions;

class UserSMSCodeIncorrectException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'SMS code is incorrect. Please try again';
        $code = 'error_wrong_sms_code';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
