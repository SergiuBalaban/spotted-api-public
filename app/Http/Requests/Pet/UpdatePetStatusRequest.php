<?php

namespace App\Http\Requests\Pet;

use App\Models\Pet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class UpdatePetStatusRequest extends FormRequest
{
    /**
     * @return array<string, array<int,In|string>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'country' => 'required|max:100',
            'city' => 'required|max:100',
            'status' => ['required', Rule::in([Pet::STATUS_MISSING, Pet::STATUS_FOUND])],
        ];
    }
}
