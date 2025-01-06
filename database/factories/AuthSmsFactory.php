<?php

namespace Database\Factories;

use App\Models\AuthSms;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthSmsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'phone' => getPhoneNumber(),
            'phone_prefix' => '+40',
            'sms_expired_at' => now()->addMinutes(AuthSms::DEFAULT_SMS_EXPIRED_AT_IN_MIN),
            'sms_code' => generateSmsCode(),
            'sms_blocked_at' => null,
            'sms_created_at' => now(),
        ];
    }
}
