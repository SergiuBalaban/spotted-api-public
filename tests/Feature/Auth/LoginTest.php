<?php

namespace Tests\Feature\Auth;

use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Models\AuthSms;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_with_valid_sms_code()
    {
        $authSms = AuthSms::factory()->create();
        $payload = [
            'phone' => $authSms->phone,
            'sms_code' => $authSms->sms_code,
        ];
        $response = $this->post(route('auth.apiLogin'), $payload);
        $responseData = $response->assertOk()->json();
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('token_type', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);
    }

    public function test_login_with_invalid_sms_code()
    {
        $response = $this->post(route('auth.apiLogin'), ['phone' => getPhoneNumber(), 'sms_code' => generateSmsCode()]);
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_SMS_MESSAGE, ErrorMessageValue::ERROR_SMS_CODE);
    }

    public function test_login_with_valid_sms_code_and_different_phone_number()
    {
        $authSms = AuthSms::factory()->create();
        $payload = [
            'phone' => getPhoneNumber(),
            'sms_code' => $authSms->sms_code,
        ];
        $response = $this->post(route('auth.apiLogin'), $payload);
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_SMS_MESSAGE, ErrorMessageValue::ERROR_SMS_CODE);
    }

    public function test_login_with_expired_sms_code()
    {
        $authSms = AuthSms::factory()->create([
            'sms_expired_at' => now()->subDay(),
        ]);
        $payload = [
            'phone' => $authSms->phone,
            'sms_code' => $authSms->sms_code,
        ];
        $response = $this->post(route('auth.apiLogin'), $payload);
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_SMS_EXPIRED_MESSAGE, ErrorMessageValue::ERROR_SMS_EXPIRED_CODE);
    }

    public function test_login_with_blocked_sms_code_before_blocked_time()
    {
        $authSms = AuthSms::factory()->create([
            'sms_attempts' => AuthSms::DEFAULT_SMS_MAX_ATTEMPTS + 1,
            'sms_blocked_at' => now()->addHour(),
        ]);
        $payload = [
            'phone' => $authSms->phone,
            'sms_code' => $authSms->sms_code,
        ];
        $response = $this->post(route('auth.apiLogin'), $payload);
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData,
            ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_MESSAGE,
            ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_CODE
        );
        $this->assertTrue($authSms->is_blocked);
    }
}
