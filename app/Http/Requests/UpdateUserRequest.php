<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes',
            'email' => 'sometimes|unique:users,email',
            'avatar' => 'sometimes',
            'timezone' => 'sometimes',
        ];
    }
}
