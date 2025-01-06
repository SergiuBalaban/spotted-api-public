<?php

namespace App\Exceptions\CustomMessages\SMS;

use App\Exceptions\CustomMessages\AbstractForbiddenException;
use App\Exceptions\CustomMessages\ErrorMessageValue;

class ProfileBlockedAfterToManySMSAttemptsException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_CODE;
}
