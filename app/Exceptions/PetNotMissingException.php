<?php

namespace App\Exceptions;

class PetNotMissingException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'This pet is not missing';
        $code = 'error_pet_not_missing';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
