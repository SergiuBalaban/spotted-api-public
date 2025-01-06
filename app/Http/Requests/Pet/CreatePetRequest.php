<?php

namespace App\Http\Requests\Pet;

use App\Models\Pet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class CreatePetRequest extends FormRequest
{
    /**
     * @return array<string, array<int,In|string>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => 'required',
            'sex' => ['required', Rule::in([Pet::SEX_M, Pet::SEX_F])],
            'category' => ['required', Rule::in([Pet::CATEGORY_DOG, Pet::CATEGORY_CAT])],
            'species' => 'required',
            'file' => 'sometimes|nullable',
        ];
    }
}
