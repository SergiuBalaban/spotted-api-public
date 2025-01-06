<?php

namespace App\Http\Requests\Pet;

use App\Models\Pet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class UpdatePetRequest extends FormRequest
{
    /**
     * @return array<string, array<int,In|string>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => 'sometimes',
            'sex' => ['sometimes', Rule::in([Pet::SEX_M, Pet::SEX_F])],
            'file' => 'sometimes|nullable',
            'species' => 'sometimes',
        ];
    }
}
