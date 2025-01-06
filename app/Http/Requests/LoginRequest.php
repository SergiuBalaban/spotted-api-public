<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|min:10|regex:/^[0-9\+]{7,15}$/',
            'code_country' => 'sometime',
            'sms_code' => 'required',
        ];
    }
}
