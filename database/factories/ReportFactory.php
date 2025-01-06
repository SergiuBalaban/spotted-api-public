<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\Report;

class ReportFactory extends FactoryAbstract
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'country' => $this->faker->country,
            'city' => $this->faker->city,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'formatted_address' => $this->faker->address,
            'category' => getPetCategory(),
            'message' => $this->faker->text,
            'status' => Report::STATUS_REPORTED,
        ];
    }

    public function markedAs(string $status = Report::STATUS_MISSING): ReportFactory
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }

    public function withPet(?Pet $pet = null): ReportFactory
    {
        return $this->state(function (array $attributes) use ($pet) {
            if (! $pet) {
                $pet = Pet::factory()->create([
                    'user_id' => $attributes['user_id'],
                    'status' => $attributes['status'],
                    'category' => $attributes['category'],
                ]);
            }

            return [
                'pet_id' => $pet->id,
                'status' => $pet->status,
            ];
        });
    }
}
