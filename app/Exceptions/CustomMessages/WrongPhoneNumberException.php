<?php

namespace App\Exceptions\CustomMessages;

class WrongPhoneNumberException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_PHONE_NUMBER_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_PHONE_NUMBER_CODE;
}
