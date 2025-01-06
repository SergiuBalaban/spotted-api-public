<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    public function test_refresh_token_with_auth_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('apiRefresh'));
        $responseData = $response->assertOk()->json();
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('token_type', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);
    }

    public function test_refresh_token_without_auth_user()
    {
        User::factory()->create();
        $response = $this->post(route('apiRefresh'));
        $response->assertUnauthorized();
    }
}
