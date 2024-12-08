<?php

namespace App\Libraries;

use App\Mail\SendEmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserService
{

    /** @var User $user */
    private $user;

    public function __construct(User $user = null) {
        $this->user = $user;
    }

    /**
     * Create new User Model
     *
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $phone = $request->phone;
        if (strpos($request->phone, '+') === false) {
            $phone = "+".$phone;
        }
        return User::create([
            'phone' => $phone,
            'country_code' => User::ROMANIA_COUNTRY_CODE,
            'active' => 1,
        ]);
    }

    /**
     * Send verification token to user
     *
     * @param $email
     * @return User|bool
     * @throws \Throwable
     */
    public function sendVerificationToken($email)
    {
        if(!$this->user || !$email) return false;
        Mail::to($this->user)->send(new SendEmailVerification($this->user));
        return $this->user;
    }
}
