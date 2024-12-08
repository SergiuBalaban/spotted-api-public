<?php

namespace App\Exceptions;

class MaximumAmountOfReportsException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'You have exceded the maximum amount of reports for today';
        $code = 'error_max_reports_reached';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
