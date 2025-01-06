<?php

namespace App\Jobs;

use App\Mail\SendEmailVerification;
use App\Models\AuthSms;
use App\Notifications\SMSNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendValidationSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?AuthSms $authSms;

    public function __construct(int $authId)
    {
        $this->authSms = AuthSms::query()->find($authId);
    }

    public function handle(): void
    {
        if ($this->authSms) {
            $this->authSms->notify(new SMSNotification);
            Mail::to('sergiu.balaban92@gmail.com')->send(new SendEmailVerification($this->authSms));
        }
    }
}
