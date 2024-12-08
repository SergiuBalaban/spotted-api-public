<?php

namespace App\Exceptions;

class UserSMSCodeExpiredException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'Your sms code was expired';
        $code = 'error_expired_sms_code';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
