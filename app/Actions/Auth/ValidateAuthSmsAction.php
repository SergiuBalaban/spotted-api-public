<?php

namespace App\Actions\Auth;

use App\Exceptions\CustomMessages\SMS\ProfileBlockedAfterToManySMSAttemptsException;
use App\Http\Requests\VerificationRequest;
use App\Libraries\AuthSmsService;

class ValidateAuthSmsAction
{
    /**
     * @return array<string, bool|int|string|null>
     *
     * @throws ProfileBlockedAfterToManySMSAttemptsException
     */
    public function run(VerificationRequest $request): array
    {
        $phone = trim($request->get('phone'));

        $authSmsService = new AuthSmsService;
        $authSms = $authSmsService->checkPhoneAndSendSms($phone);

        return [
            'sms_code' => $authSms->sms_code,
            'sms_attempts' => $authSms->sms_attempts,
            'sms_expired_at' => $authSms->sms_expired_at,
            'sms_blocked' => $authSms->is_blocked,
        ];
    }
}
