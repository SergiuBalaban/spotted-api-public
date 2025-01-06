<?php

namespace App\Exceptions\CustomMessages;

class PetMissingException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_MISSING_PET_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_MISSING_PET_CODE;
}
