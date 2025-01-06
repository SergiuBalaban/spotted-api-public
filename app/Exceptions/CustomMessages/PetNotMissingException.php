<?php

namespace App\Exceptions\CustomMessages;

class PetNotMissingException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_NOT_MISSING_PET_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_NOT_MISSING_PET_CODE;
}
