<?php

namespace App\Actions\Auth;

use App\Exceptions\CustomMessages\SMS\ProfileBlockedAfterToManySMSAttemptsException;
use App\Exceptions\CustomMessages\UserSMSCodeIncorrectException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserSMSCodeExpiredException;
use App\Http\Requests\VerificationRequest;
use App\Models\AuthSms;
use App\Models\User;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginAction
{
    /**
     * @throws ProfileBlockedAfterToManySMSAttemptsException
     * @throws UserSMSCodeExpiredException
     * @throws UserSMSCodeIncorrectException
     * @throws UnauthorizedException
     */
    public function run(VerificationRequest $request): string
    {
        Session::flush();
        $authSms = AuthSms::query()
            ->where('phone', trim($request->phone))
            ->where('sms_code', $request->sms_code)
            ->first();

        if (! $authSms) {
            throw new UserSMSCodeIncorrectException;
        }

        if ($authSms->sms_expired) {
            throw new UserSMSCodeExpiredException;
        }

        if ($authSms->is_blocked) {
            throw new ProfileBlockedAfterToManySMSAttemptsException;
        }

        $payload = ['phone' => $authSms->phone];
        /** @var User $user */
        $user = User::query()->firstOrCreate($payload);

        auth()->login($user);
        $authUser = app(GetAuthenticatedUserTask::class)->run();
        if ($authUser->id !== $user->id) {
            throw new UserSMSCodeIncorrectException;
        }
        $token = Auth::tokenById($user->id);
        if (! $token) {
            throw new UnauthorizedException;
        }

        return $token;
    }
}
