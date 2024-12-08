<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @param AdminLoginRequest $request
     * @return JsonResponse
     * @throws UnauthorizedException
     */
    public function login(AdminLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['admin'] = true;

        if (!$token = Auth::attempt($credentials)) {
            throw new UnauthorizedException('Email or password combination is incorrect');
        }

        JWTAuth::factory()->setTTL(525600);
        $data = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Carbon::now()->addYear()->timestamp,
        ];

        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();
        return $this->response->send();
    }
}
