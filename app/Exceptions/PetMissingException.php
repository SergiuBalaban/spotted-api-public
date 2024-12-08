<?php

namespace App\Exceptions;

class PetMissingException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'This pet is already missing';
        $code = 'error_pet_missing';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
