<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UserSMSCodeExpiredException;
use App\Exceptions\UserSMSCodeIncorrectException;
use App\Http\Requests\VerificationRequest;
use App\Libraries\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\App;
use JWTAuth;
use Nexmo\Laravel\Facade\Nexmo;

class AuthController extends Controller
{
    /**
     * @param VerificationRequest $request
     * @return JsonResponse
     */
    public function verification(VerificationRequest $request)
    {
        $phone = trim($request->get('phone'));

        // TODO: Check phone number if valid for nexmo
        $user = User::wherePhone($phone)->orWhere('phone', 'LIKE', '%'.$phone.'%')->first();

        if(!$user) {
            $userService = new UserService();
            $user = $userService->create($request);
        }

        $smsCode = generateSmsCode();
        date_default_timezone_set('Europe/Bucharest');
        $expirationCode = Carbon::now()->addMinutes(User::DEFAULT_VALID_EXPIRATION_CODE_IN_MIN);
        $user->update(['sms_code' => $smsCode, 'sms_code_expiration' => $expirationCode]);

        if(App::isProduction() && !in_array($phone, User::DEFAULT_PHONES_FOR_TESTING)) {
            Nexmo::message()->send([
                'to' => $user->phone,
                'from' => 'Spotted APP',
                'text' => 'Codul tău de verificare pentru Spotted App: '. $smsCode .'. Va exipra în '.User::DEFAULT_VALID_EXPIRATION_CODE_IN_MIN.' minute.'
            ]);
            $smsCode = null;
        }

        return response()->json(['sms_code' => $smsCode], 200);
    }

    /**
     * Get a JWT via given credentials, and user details.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ForbiddenException
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('phone', trim($request->phone))
            ->where('sms_code', $request->sms_code)
            ->first();

        if(!$user) {
            throw new UserSMSCodeIncorrectException();
        }

        if($user->sms_code_expiration < Carbon::now()) {
            throw new UserSMSCodeExpiredException();
        }

        JWTAuth::factory()->setTTL(525600);
        if (!$token = auth()->login($user)) {
            throw new UserSMSCodeIncorrectException();
        }

        $user->update(['active' => 1]);
        return $this->response->setJwtToken($token)->send();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return $this->response->send();
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        JWTAuth::factory()->setTTL(525600);
        return $this->response->setJwtToken(auth()->refresh())->send();
    }
}
