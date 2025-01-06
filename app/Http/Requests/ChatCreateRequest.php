<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatCreateRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'data' => 'required|max:255',
            'receiver_id' => 'required|exists:users,id,deleted_at,NULL',
        ];
    }
}
