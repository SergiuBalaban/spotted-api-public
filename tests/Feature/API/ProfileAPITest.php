<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;

class ProfileAPITest extends TestCase
{
    public function test_get_profile_without_auth()
    {
        User::factory()->create();
        $response = $this->get(route('profile.apiGetProfile'));
        $response->assertUnauthorized();
    }

    public function test_get_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('profile.apiGetProfile'));
        $responseData = $response->assertOk()->json();
        $this->checkUserResponseData($responseData);
    }

    public function test_update_profile_name()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $oldName = $user->name;
        $newName = fake()->name;
        $payload = [
            'name' => $newName,
        ];
        $response = $this->patch(route('profile.apiUpdateProfile'), $payload);
        $responseData = $response->assertOk()->json();
        $this->checkUserResponseData($responseData);
        $this->assertEquals($user->name, $newName);
        $this->assertDatabaseMissing(User::class, ['name' => $oldName]);
    }

    public function test_delete_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->delete(route('profile.apiDeleteProfile'));
        $response->assertStatus(204)->assertNoContent();
        $this->assertDatabaseCount(User::class, 0);
    }

    private function checkUserResponseData(array $responseData): void
    {
        $keys = array_merge(['id'], User::POSSIBLE_UPDATED_FIELDS);
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $responseData);
        }
    }
}
