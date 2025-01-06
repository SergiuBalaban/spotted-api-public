<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;

class CreateGalleryPetRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'file' => 'required',
            'pet_id' => 'required',
        ];
    }
}
