<?php

namespace App\Mail;

use App\Models\AuthSms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailVerification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected AuthSms $authSms) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation for '.config('app.name'),
        );
    }

    public function build(): SendEmailVerification
    {
        return $this
            ->markdown('emails.user-email-verification', [
                'invite_link' => $this->authSms->invite_token,
                'user_name' => 'Test user',
            ]);
    }
}
