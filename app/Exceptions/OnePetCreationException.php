<?php

namespace App\Exceptions;

class OnePetCreationException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'You can have only one pet';
        $code = 'error_one_pet_creation';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
