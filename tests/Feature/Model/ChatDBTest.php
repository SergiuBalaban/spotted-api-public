<?php

namespace Tests\Feature\Model;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Tests\TestCase;

class ChatDBTest extends TestCase
{
    public function test_create_chat()
    {
        $chat = Chat::factory()->start()->create();
        $this->assertDatabaseCount(User::class, 2);
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(Report::class, 2);
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertDatabaseCount(Message::class, 0);

        $this->assertDatabaseHas(Chat::class, [
            'id' => $chat->id,
            'owner_id' => $chat->owner_id,
            'reporter_id' => $chat->reporter_id,
            'report_found_id' => $chat->report_found_id,
            'report_missing_id' => $chat->report_missing_id,
            'active' => 0,
        ]);

        $this->assertDatabaseHas(Report::class, [
            'id' => $chat->report_found_id,
            'pet_id' => null,
            'status' => Report::STATUS_REPORTED,
        ]);

        $this->assertDatabaseHas(Report::class, [
            'id' => $chat->report_missing_id,
            'pet_id' => $chat->reportMissing->pet_id,
            'status' => Report::STATUS_MISSING,
        ]);

        $this->assertDatabaseHas(Pet::class, [
            'id' => $chat->reportMissing->pet_id,
            'status' => Report::STATUS_MISSING,
        ]);
    }

    public function test_create_chat_with_message()
    {
        $chat = Chat::factory()->start()->withMessage()->create();
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertDatabaseCount(Message::class, 1);

        $message = Message::query()->first();
        $this->assertDatabaseHas(Message::class, [
            'id' => $message->id,
            'chat_id' => $chat->id,
        ]);
    }
}
