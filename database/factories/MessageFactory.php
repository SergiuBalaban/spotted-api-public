<?php

namespace Database\Factories;

class MessageFactory extends FactoryAbstract
{
    public function definition(): array
    {
        return [
            'data' => $this->faker->text,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
