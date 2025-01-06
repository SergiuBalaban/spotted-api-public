<?php

namespace App\Exceptions\CustomMessages;

class ReportedPetSameLocationException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_DUPLICATE_REPORT_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_DUPLICATE_REPORT_CODE;
}
