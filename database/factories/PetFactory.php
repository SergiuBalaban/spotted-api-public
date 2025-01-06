<?php

namespace Database\Factories;

use App\Models\Pet;

class PetFactory extends FactoryAbstract
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'nickname' => $this->faker->name,
            'category' => getPetCategory(),
            'sex' => getPetSex(),
            'species' => $this->faker->name,
            'status' => Pet::STATUS_NORMAL,
        ];
    }

    public function asMissing(): PetFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Pet::STATUS_MISSING,
            ];
        });
    }
}
