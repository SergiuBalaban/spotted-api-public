<?php

namespace App\Libraries;

use App\Exceptions\CustomMessages\SMS\ProfileBlockedAfterToManySMSAttemptsException;
use App\Jobs\SendValidationSmsJob;
use App\Models\AuthSms;

class AuthSmsService extends AbstractService
{
    /**
     * @throws ProfileBlockedAfterToManySMSAttemptsException
     */
    public function checkPhoneAndSendSms(string $phone): AuthSms
    {
        $authSms = $this->firstOrCreate($phone);
        $authSms = $this->generateCode($authSms);
        $authSms = $this->setAsBlocked($authSms);
        $authSms = $this->setAsUnBlocked($authSms);

        if ($authSms->is_blocked) {
            throw new ProfileBlockedAfterToManySMSAttemptsException;
        }

        SendValidationSmsJob::dispatch($authSms->id);

        return $authSms;
    }

    private function firstOrCreate(string $phone): mixed
    {
        if (! str_contains($phone, '+')) {
            $phone = '+'.$phone;
        }

        return AuthSms::query()->firstOrCreate([
            'phone' => $phone,
        ]);
    }

    private function generateCode(AuthSms $authSms): AuthSms
    {
        $authSms->sms_code = generateSmsCode();
        $authSms->sms_attempts += 1;
        $authSms->sms_created_at = now();
        $authSms->sms_expired_at = isset($authSms->sms_expired_at) ? $authSms->sms_expired_at : now()->addMinutes(AuthSms::DEFAULT_SMS_EXPIRED_AT_IN_MIN);
        $authSms->save();

        return $authSms;
    }

    private function setAsBlocked(AuthSms $authSms): AuthSms
    {
        if ($authSms->no_attempts_left && ! $authSms->sms_blocked_at) {
            $authSms->sms_blocked_at = now()->addHour();
            $authSms->save();
        }

        return $authSms;
    }

    private function setAsUnBlocked(AuthSms $authSms): AuthSms
    {
        if ($authSms->no_attempts_left && ! $authSms->is_blocked) {
            $authSms->sms_attempts = 1;
            $authSms->sms_blocked_at = null;
            $authSms->save();
        }

        return $authSms;
    }
}
