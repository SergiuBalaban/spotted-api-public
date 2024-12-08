<?php

namespace App\Exceptions;

class WrongPhoneNumberException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'Wrong format phone number';
        $code = 'error_wrong_phone_number';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
