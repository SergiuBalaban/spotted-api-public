<?php

namespace Tests\Feature\API;

use App\Events\GetMissingPetsEvent;
use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Models\Chat;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PetAPITest extends TestCase
{
    // Get user pets
    public function test_get_user_pets_with_0_pets()
    {
        //Given
        $user = User::factory()->create();

        //When
        $response = $this->actingAs($user)->getJson(route('pets.apiGetPets'));

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertCount(0, $responseData);
    }

    public function test_get_user_pets_with_1_pet()
    {
        //Given
        $pet = Pet::factory()->asNewUser()->create();
        $user = $pet->user;

        //When
        $response = $this->actingAs($user)->getJson(route('pets.apiGetPets'));

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertCount(1, $responseData);
    }

    public function test_get_user_pets_with_2_pet()
    {
        //Given
        $user = User::factory()->create();
        Pet::factory(2)->asUser($user)->create();

        //When
        $response = $this->actingAs($user)->getJson(route('pets.apiGetPets'));

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertCount(2, $responseData);
    }

    // Create user pet
    public function test_create_user_pets_without_pet()
    {
        //Given
        $user = User::factory()->create();
        $payload = Pet::factory()->make()->toArray();
        unset($payload['user_id']);

        //When
        $response = $this->actingAs($user)->postJson(route('pets.apiCreatePet'), $payload);

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertDatabaseHas(Pet::class, [
            'id' => $responseData['id'],
            'nickname' => $payload['nickname'],
            'category' => $payload['category'],
            'sex' => $payload['sex'],
            'species' => $payload['species'],
        ]);
    }

    public function test_create_user_pets_with_pet()
    {
        //Given
        $user = User::factory()->create();
        Pet::factory()->asUser($user)->create();
        $payload = Pet::factory()->make()->toArray();
        unset($payload['user_id']);

        //When
        $response = $this->actingAs($user)->postJson(route('pets.apiCreatePet'), $payload);

        //Then
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_CREATE_PET_MESSAGE, ErrorMessageValue::ERROR_CREATE_PET_CODE);
    }

    // Get user pet
    public function test_get_not_auth_user_pet()
    {
        //Given
        $pet = Pet::factory()->asNewUser()->create();

        //When
        $response = $this->getJson(route('pets.apiFindPetByID', ['pet' => $pet]));

        //Then
        $response->assertUnauthorized();
    }

    public function test_get_auth_user_pet()
    {
        //Given
        $user = User::factory()->create();
        $pet = Pet::factory()->asUser($user)->create();

        //When
        $response = $this->actingAs($user)->getJson(route('pets.apiFindPetByID', ['pet' => $pet]));

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertEquals($responseData['id'], $pet->id);
        foreach (Pet::POSSIBLE_UPDATED_FIELDS as $key) {
            $this->assertEquals($responseData[$key], $pet->$key);
        }
    }

    public function test_get_user_pet_from_different_user()
    {
        //Given
        $user = User::factory()->create();
        $pet = Pet::factory()->asNewUser()->create();

        //When
        $response = $this->actingAs($user)->getJson(route('pets.apiFindPetByID', ['pet' => $pet]));

        //Then
        $response->assertUnauthorized();
    }

    // Update pet
    public function test_patch_not_auth_user_pet()
    {
        //Given
        $pet = Pet::factory()->asNewUser()->create();

        //When
        $response = $this->patchJson(route('pets.apiUpdatePet', ['pet' => $pet]));

        //Then
        $response->assertUnauthorized();
    }

    public function test_patch_auth_user_pet()
    {
        //Given
        $user = User::factory()->create();
        $pet = Pet::factory()->asUser($user)->create();
        $payload = Pet::factory()->make(['status' => Pet::STATUS_MISSING])->toArray();
        unset($payload['user_id']);

        //When
        $response = $this->actingAs($user)->patchJson(route('pets.apiUpdatePet', ['pet' => $pet]), $payload);

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertDatabaseHas(Pet::class, [
            'id' => $responseData['id'],
            'nickname' => $payload['nickname'],
            'category' => $payload['category'],
            'sex' => $payload['sex'],
            'species' => $payload['species'],
        ]);
    }

    // Update pet status
    public function test_update_pet_status_from_normal_to_missing()
    {
        //Given
        $status = Pet::STATUS_MISSING;
        $user = User::factory()->create();
        $pet = Pet::factory()->asUser($user)->create();

        //When
        $payload = Report::factory(['status' => $status])->make()->toArray();
        $response = $this->actingAs($user)->patchJson(route('pets.apiUpdatePetStatus', ['pet' => $pet]), $payload);

        //Then
        $response->assertNoContent();
        $this->assertDatabaseHas(Pet::class, [
            'id' => $pet->id,
            'status' => $status,
        ]);
    }

    public function test_update_pet_status_from_missing_to_found()
    {
        //Given
        $user = User::factory()->create();
        $pet = Pet::factory()->asUser($user)
            ->has(Report::factory([
                'user_id' => $user->id,
                'status' => Pet::STATUS_MISSING,
            ]), 'report')
            ->create([
                'status' => Pet::STATUS_MISSING,
            ]);
        $missingPetReport = $pet->report;

        //When
        $payload = Report::factory(['status' => Pet::STATUS_FOUND])->make()->toArray();
        $response = $this->actingAs($user)->patchJson(route('pets.apiUpdatePetStatus', ['pet' => $pet]), $payload);

        //Then
        $response->assertNoContent();
        $pet->refresh();
        $missingPetReport->refresh();
        $this->assertSoftDeleted($missingPetReport);
        $this->assertDatabaseHas(Pet::class, [
            'id' => $pet->id,
            'status' => Pet::STATUS_FOUND,
        ]);
    }

    public function test_trigger_job_when_pet_is_missing()
    {
        //Given
        Event::fake([
            GetMissingPetsEvent::class,
        ]);
        $user = User::factory()->create();
        $reporter = User::factory()->create();
        $pet = Pet::factory()->asUser($user)->create([
            'category' => Pet::CATEGORY_DOG,
        ]);
        $reportPayload = [
            'category' => Pet::CATEGORY_DOG,
            'city' => fake()->city(),
            'country' => fake()->country(),
        ];
        Report::factory()->asUser($reporter)->create($reportPayload);

        //When
        $payload = Report::factory(['status' => Pet::STATUS_MISSING])->make($reportPayload)->toArray();
        $response = $this->actingAs($user)->patchJson(route('pets.apiUpdatePetStatus', ['pet' => $pet]), $payload);

        //Then
        $response->assertNoContent();
        $firstChat = Chat::query()->first();
        $this->assertTrue((bool) $firstChat->active);
        Event::assertDispatched(GetMissingPetsEvent::class, 1);
    }

    public function test_trigger_job_when_pet_is_found()
    {
        //Given
        Event::fake([
            GetMissingPetsEvent::class,
        ]);
        $chat = Chat::factory()->start()->create();

        //When
        $pet = $chat->reportMissing->pet;
        $payload = Report::factory(['status' => Pet::STATUS_FOUND])->make([
            'category' => $chat->reportMissing->category,
            'country' => $chat->reportMissing->country,
            'city' => $chat->reportMissing->city,
        ])->toArray();
        $response = $this->actingAs($chat->owner)->patchJson(route('pets.apiUpdatePetStatus', ['pet' => $pet]), $payload);

        //Then
        $response->assertNoContent();
        $chat->refresh();
        $this->assertNotTrue($chat->active);
        Event::assertDispatched(GetMissingPetsEvent::class, 1);
    }

    // Delete auth user pet
    public function test_delete_not_auth_user_pet()
    {
        //Given
        $pet = Pet::factory()->asNewUser()->create();

        //When
        $response = $this->deleteJson(route('pets.apiDeletePet', ['pet' => $pet]));

        //Then
        $response->assertUnauthorized();
    }

    public function test_delete_auth_user_pet()
    {
        //Given
        $user = User::factory()->create();
        $pet = Pet::factory()->asUser($user)->create();

        //When
        $response = $this->actingAs($user)->deleteJson(route('pets.apiDeletePet', ['pet' => $pet]));

        //Then
        $response->assertNoContent();
        $this->assertSoftDeleted($pet);
    }
}
