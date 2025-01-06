<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;

class ChatFactory extends FactoryAbstract
{
    public function definition(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function start(): ChatFactory
    {
        return $this->state(function (array $attributes) {
            $payload = Report::factory()->make()->toArray();
            $sender = User::factory()->create();
            $reporter = User::factory()->create();

            return [
                'owner_id' => $sender->id,
                'reporter_id' => $reporter->id,
                'report_found_id' => Report::factory($payload)->asUser($reporter)->create(),
                'report_missing_id' => Report::factory(array_merge($payload, ['status' => Report::STATUS_MISSING]))->asUser($sender)->withPet()->create(),
            ];
        });
    }

    public function withMessage(): ChatFactory
    {
        return $this->afterCreating(function (Chat $chat) {
            Message::factory([
                'chat_id' => $chat->id,
                'sender_id' => $chat->owner_id,
                'receiver_id' => $chat->reporter_id,
            ])->create();
        });
    }
}
