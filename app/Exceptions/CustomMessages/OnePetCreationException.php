<?php

namespace App\Exceptions\CustomMessages;

class OnePetCreationException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_CREATE_PET_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_CREATE_PET_CODE;
}
