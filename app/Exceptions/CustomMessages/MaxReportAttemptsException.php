<?php

namespace App\Exceptions\CustomMessages;

class MaxReportAttemptsException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_MAX_REPORTS_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_MAX_REPORTS_CODE;
}
