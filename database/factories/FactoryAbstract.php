<?php

namespace Database\Factories;

use App\Models\User;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class FactoryAbstract extends Factory
{
    public function asNewUser(): FactoryAbstract
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory()->create()->id,
            ];
        });
    }

    public function asUser(User $user): FactoryAbstract
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }

    public function asLoginUser(): FactoryAbstract
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
