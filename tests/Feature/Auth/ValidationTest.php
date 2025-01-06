<?php

namespace Tests\Feature\Auth;

use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Jobs\SendValidationSmsJob;
use App\Mail\SendEmailVerification;
use App\Models\AuthSms;
use App\Models\User;
use App\Notifications\SMSNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Notification::fake();
        Mail::fake();
    }

    public function test_validation_with_existing_user()
    {
        $user = User::factory()->create();
        $payload = [
            'phone' => $user->phone,
        ];
        $response = $this->post(route('auth.apiPhoneVerification'), $payload);
        $response->assertOk();
        $authSms = $this->checkAuthSms();
        $this->checkAuthSmsResponseData($response, $authSms);
    }

    public function test_validation_without_existing_user()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        $response = $this->post(route('auth.apiPhoneVerification'), $payload);
        $response->assertOk();
        $authSms = $this->checkAuthSms();
        $this->checkAuthSmsResponseData($response, $authSms);
    }

    public function test_validation_with_success_loop_tries_to_login()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        for ($i = 1; $i <= AuthSms::DEFAULT_SMS_MAX_ATTEMPTS; $i++) {
            $response = $this->post(route('auth.apiPhoneVerification'), $payload);
            $response->assertOk();
            $authSms = $this->checkAuthSms();
            $this->assertEquals($i, $authSms->sms_attempts);
            $this->checkAuthSmsResponseData($response, $authSms);
        }
    }

    public function test_validation_with_failed_loop_tries_to_login()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        $totalAttempts = AuthSms::DEFAULT_SMS_MAX_ATTEMPTS + 1;
        for ($i = 1; $i <= $totalAttempts; $i++) {
            $response = $this->post(route('auth.apiPhoneVerification'), $payload);

            if ($i >= $totalAttempts) {
                $response->assertStatus(403);
                $responseData = $response->json();
                $this->checkFailedResponseData(
                    $responseData,
                    ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_MESSAGE,
                    ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_CODE
                );
                $authSms = $this->checkAuthSms();
                $this->assertEquals($authSms->sms_blocked_at->toString(), now()->addHour()->toString());

                continue;
            }

            $response->assertOk();
            $authSms = $this->checkAuthSms();
            $this->assertEquals($i, $authSms->sms_attempts);
            $this->checkAuthSmsResponseData($response, $authSms);
        }
    }

    public function test_validation_with_blocked_sms()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        AuthSms::factory()->create([
            'phone' => $payload['phone'],
            'sms_attempts' => AuthSms::DEFAULT_SMS_MAX_ATTEMPTS + 1,
            'sms_blocked_at' => now()->addMinute(),
        ]);
        $response = $this->post(route('auth.apiPhoneVerification'), $payload);
        $response->assertStatus(403);
        $responseData = $response->json();
        $this->checkFailedResponseData(
            $responseData,
            ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_MESSAGE,
            ErrorMessageValue::ERROR_USER_BLOCKED_AFTER_SMS_ATTEMPTS_CODE
        );
        $authSms = $this->checkAuthSms();
        $this->assertTrue($authSms->is_blocked);
    }

    public function test_validation_with_unblocked_sms()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        AuthSms::factory()->create([
            'phone' => $payload['phone'],
            'sms_attempts' => AuthSms::DEFAULT_SMS_MAX_ATTEMPTS + 1,
            'sms_blocked_at' => now()->subMinute(),
        ]);
        $response = $this->post(route('auth.apiPhoneVerification'), $payload);
        $response->assertOk();
        $authSms = $this->checkAuthSms();
        $this->assertEquals(1, $authSms->sms_attempts);
        $this->checkAuthSmsResponseData($response, $authSms);
    }

    public function test_validation_to_trigger_sms()
    {
        $payload = [
            'phone' => getPhoneNumber(),
        ];
        $response = $this->post(route('auth.apiPhoneVerification'), $payload);
        $response->assertOk();
        $authSms = $this->checkAuthSms();

        $job = new SendValidationSmsJob($authSms->id);
        $job->handle();

        $mail = new SendEmailVerification($authSms);
        Mail::send($mail);

        Mail::assertQueued(SendEmailVerification::class, function ($mail) {
            return $mail->hasSubject(subject: 'Invitation for '.config('app.name'));
        });

        Notification::assertSentTo(
            [$authSms], SMSNotification::class
        );
        Notification::assertCount(1);

        Queue::assertPushed(SendValidationSmsJob::class, function ($job) use ($authSms) {
            return $job->authSms->id === $authSms->id;
        });
    }

    private function checkAuthSms(): AuthSms
    {
        $authSms = AuthSms::latest()->first();
        $this->assertDatabaseHas(AuthSms::class,
            [
                'id' => $authSms->id,
                'phone' => $authSms->phone,
                'sms_code' => $authSms->sms_code,
                'sms_attempts' => $authSms->sms_attempts,
                'sms_created_at' => $authSms->sms_created_at->format('Y-m-d H:i:s'),
                'sms_expired_at' => $authSms->sms_expired_at->format('Y-m-d H:i:s'),
                'sms_blocked_at' => $authSms->sms_blocked_at ? $authSms->sms_blocked_at->format('Y-m-d H:i:s') : null,
            ]
        );

        return $authSms;
    }

    private function checkAuthSmsResponseData($response, AuthSms $authSms): void
    {
        $response->assertJsonStructure(['sms_code', 'sms_attempts', 'sms_expired_at']);
        $responseData = $response->json();
        $this->assertEquals($responseData['sms_code'], $authSms->sms_code);
        $this->assertEquals($responseData['sms_attempts'], $authSms->sms_attempts);
    }
}
