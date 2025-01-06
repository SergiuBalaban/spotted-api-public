<?php

namespace App\Exceptions\CustomMessages;

class UserHaveNoMissingPetsException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_NOT_MISSING_PET_FROM_USER_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_NOT_MISSING_PET_FROM_USER_CODE;
}
