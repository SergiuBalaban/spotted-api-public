<?php

namespace App\Http\Requests;

use App\Models\Pet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PetCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nickname' => 'required',
            'sex'      => ['required', Rule::in([Pet::SEX_M, Pet::SEX_F])],
            'file'     => 'sometimes|nullable',
            'category' => ['required', Rule::in([Pet::CATEGORY_DOG, Pet::CATEGORY_CAT])],
            'species'  => 'required',
        ];
    }
}
