<?php

namespace App\Exceptions\CustomMessages;

class UserSMSCodeIncorrectException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_SMS_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_SMS_CODE;
}
