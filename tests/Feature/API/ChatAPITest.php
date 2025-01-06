<?php

namespace Tests\Feature\API;

use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Tests\TestCase;

class ChatAPITest extends TestCase
{
    // GET chats
    public function test_user_can_get_0_chats()
    {
        //Given
        $user = User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, 0);
        $this->assertDatabaseCount(Chat::class, 0);
        $this->assertDatabaseCount(Message::class, 0);

        //When
        $response = $this->actingAs($user)->getJson(route('chats.apiGetUserChats'));

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_owner_can_get_1_chats_without_messages()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $responseAsOwner = $this->actingAs($chat->owner)->getJson(route('chats.apiGetUserChats'));
        $responseAsReporter = $this->actingAs($chat->reporter)->getJson(route('chats.apiGetUserChats'));

        //Then
        $responseFirstElement = $responseAsOwner->assertOk()->assertJsonCount(1)->json()[0];
        $this->assertIsNotArray($responseFirstElement['last_message']);

        $responseFirstElement = $responseAsReporter->assertOk()->assertJsonCount(1)->json()[0];
        $this->assertIsNotArray($responseFirstElement['last_message']);
    }

    public function test_owner_can_get_1_chats_with_1_message()
    {
        //Given
        $chat = Chat::factory()->start()->withMessage()->create();
        $this->assertDatabaseCount(Message::class, 1);

        //When
        $responseAsOwner = $this->actingAs($chat->owner)->getJson(route('chats.apiGetUserChats'));
        $responseAsReporter = $this->actingAs($chat->reporter)->getJson(route('chats.apiGetUserChats'));

        //Then
        $responseFirstElement = $responseAsOwner->assertOk()->assertJsonCount(1)->json()[0];
        $this->assertIsArray($responseFirstElement['last_message']);

        $responseFirstElement = $responseAsReporter->assertOk()->assertJsonCount(1)->json()[0];
        $this->assertIsArray($responseFirstElement['last_message']);
    }

    public function test_owner_can_get_2_chats_without_messages()
    {
        //Given
        $senderUser = User::factory()->create();
        $receiverUser = User::factory()->create();

        $this->createChat($senderUser, $receiverUser);
        $this->createChat($receiverUser, $senderUser);

        //When
        $responseAsOwner = $this->actingAs($senderUser)->getJson(route('chats.apiGetUserChats'));
        $responseAsReporter = $this->actingAs($senderUser)->getJson(route('chats.apiGetUserChats'));

        //Then
        $responseData = $responseAsOwner->assertOk()->assertJsonCount(2)->json();
        foreach ($responseData as $responseFirstElement) {
            $this->assertIsNotArray($responseFirstElement['last_message']);
        }

        $responseData = $responseAsReporter->assertOk()->assertJsonCount(2)->json();
        foreach ($responseData as $responseFirstElement) {
            $this->assertIsNotArray($responseFirstElement['last_message']);
        }
    }

    // GET chat
    public function test_owner_can_get_chat_without_messages()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->owner)->getJson(route('chats.apiGetUserChat', ['chat' => $chat]));

        //Then
        $responseFirstElement = $response->assertOk()->json();
        $this->assertArrayHasKey('id', $responseFirstElement);
        $this->assertArrayHasKey('owner', $responseFirstElement);
        $this->assertArrayHasKey('reporter', $responseFirstElement);
        $this->assertArrayHasKey('report_found', $responseFirstElement);
        $this->assertArrayHasKey('report_missing', $responseFirstElement);
        $this->assertArrayHasKey('last_message', $responseFirstElement);
        $this->assertIsNotArray($responseFirstElement['last_message']);
    }

    public function test_owner_can_get_chat_with_message()
    {
        $chat = Chat::factory()->start()->withMessage()->create();

        //When
        $response = $this->actingAs($chat->owner)->getJson(route('chats.apiGetUserChat', ['chat' => $chat]));

        //Then
        $responseFirstElement = $response->assertOk()->json();
        $this->assertIsArray($responseFirstElement['last_message']);
    }

    public function test_user_cannot_get_other_chat()
    {
        //Given
        $user = User::factory()->create();
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($user)->getJson(route('chats.apiGetUserChat', [
            'chat' => $chat,
        ]));

        //Then
        $response->assertUnauthorized();
    }

    // DELETE chat
    public function test_owner_cannot_delete_chat_when_inactive()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->owner)->deleteJson(route('chats.apiDeleteUserChat', ['chat' => $chat]));

        //Then
        $responseData = $response->assertStatus(422)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_CHAT_INACTIVE_MESSAGE);
        $chat->refresh();
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertNotSoftDeleted($chat);
    }

    public function test_reporter_cannot_delete_chat_without_missing_pet()
    {
        //Given
        $chat = Chat::factory()->start()->create(['active' => 1]);

        //When
        $response = $this->actingAs($chat->reporter)->deleteJson(route('chats.apiDeleteUserChat', ['chat' => $chat]));

        //Then
        $responseData = $response->assertStatus(422)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_UNAUTHORIZED_MESSAGE);
        $chat->refresh();
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertNotSoftDeleted($chat);
    }

    public function test_owner_can_delete_chat()
    {
        //Given
        $chat = Chat::factory()->start()->create(['active' => 1]);

        //When
        $response = $this->actingAs($chat->owner)->deleteJson(route('chats.apiDeleteUserChat', ['chat' => $chat]));

        //Then
        $response->assertNoContent();
        $chat->refresh();
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertSoftDeleted($chat);
    }

    // PATCH chat -> set to active
    public function test_reporter_cannot_activate_chat()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->reporter)->patch(route('chats.apiActivateUserChat', ['chat' => $chat]));

        //Then
        $responseData = $response->assertStatus(422)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_UNAUTHORIZED_MESSAGE);
        $chat->refresh();
        $this->assertEquals(0, $chat->active);
    }

    public function test_owner_can_activate_chat_when_inactive()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->owner)->patch(route('chats.apiActivateUserChat', ['chat' => $chat]));

        //Then
        $response->assertNoContent();
        $chat->refresh();
        $this->assertEquals(1, $chat->active);
    }

    public function test_owner_can_activate_chat_when_active()
    {
        //Given
        $chat = Chat::factory()->start()->create(['active' => 1]);

        //When
        $response = $this->actingAs($chat->owner)->patch(route('chats.apiActivateUserChat', ['chat' => $chat]));

        //Then
        $response->assertNoContent();
        $chat->refresh();
        $this->assertEquals(1, $chat->active);
    }

    // POST message
    public function test_user_cannot_create_chat_message_when_inactive()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->owner)->postJson(route('chats.apiCreateUserChatMessage', ['chat' => $chat]));

        //Then
        $responseData = $response->assertStatus(422)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_CHAT_INACTIVE_MESSAGE);
        $this->assertDatabaseCount(Message::class, 0);
    }

    public function test_user_cannot_create_message_to_different_chat()
    {
        //Given
        $user = User::factory()->create();
        $chat = Chat::factory()->start()->create(['active' => 1]);

        //When
        $payload = [
            'data' => fake()->text,
            'receiver_id' => $chat->reporter_id,
        ];
        $response = $this->actingAs($user)->postJson(route('chats.apiCreateUserChatMessage', ['chat' => $chat]), $payload);

        //Then
        $response->assertUnauthorized();
        $this->assertDatabaseCount(Message::class, 0);
    }

    public function test_user_can_create_message()
    {
        //Given
        $chat = Chat::factory()->start()->create(['active' => 1]);

        //When
        $payload = [
            'data' => fake()->text,
            'receiver_id' => $chat->reporter_id,
        ];
        $response = $this->actingAs($chat->owner)->postJson(route('chats.apiCreateUserChatMessage', ['chat' => $chat]), $payload);

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertDatabaseHas(Message::class, [
            'id' => $responseData['id'],
            'data' => $payload['data'],
            'receiver_id' => $payload['receiver_id'],
        ]);
        $this->assertEquals($payload['data'], $responseData['data']);
    }

    private function createChat(User $sender, User $reporter, bool $withMessage = false, bool $active = false): Chat
    {
        $missingPet = Pet::factory()->asUser($sender)->asMissing()->create();
        $reportMissingPet = Report::factory()->asUser($sender)->withPet($missingPet)->create();
        $reportFoundPet = Report::factory()->asUser($reporter)->create();
        $query = Chat::factory();
        if ($withMessage) {
            $query = $query->has(Message::factory([
                'sender_id' => $sender->id,
                'receiver_id' => $reporter->id,
            ]), 'messages');
        }

        return $query->create([
            'owner_id' => $sender->id,
            'reporter_id' => $reporter->id,
            'report_found_id' => $reportFoundPet->id,
            'report_missing_id' => $reportMissingPet->id,
            'active' => $active,
        ]);
    }
}
