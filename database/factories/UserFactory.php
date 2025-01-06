<?php

namespace Database\Factories;

class UserFactory extends FactoryAbstract
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => getPhoneNumber(),
            //            'email' => $this->faker->unique()->safeEmail,
            'email' => null,
            'avatar' => null,
            'email_verified_at' => now(),
        ];
    }
}
