<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MapRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'city' => 'required',
        ];
    }
}
