<?php

namespace App\Exceptions;

class ReportedPetSameLocationException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'You already report a pet from this location';
        $code = 'report_pet_same_location';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
