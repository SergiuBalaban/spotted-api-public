<?php

namespace App\Exceptions;

class UserHaveNoMissingPetsException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'This user have no missing pets';
        $code = 'error_user_have_no_missing_pet';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
