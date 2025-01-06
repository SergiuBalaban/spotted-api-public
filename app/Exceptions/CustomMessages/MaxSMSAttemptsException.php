<?php

namespace App\Exceptions\CustomMessages;

class MaxSMSAttemptsException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_SMS_MAX_ATTEMPTS_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_SMS_MAX_ATTEMPTS_CODE;

    /**
     * MaxSMSAttemptsException constructor.
     */
    public function __construct(string $text)
    {
        $this->message = parseText('[MINUTES]', $text, $this->message);
        parent::__construct();
    }
}
