<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_logout_with_auth_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post(route('apiLogout'));
        $response->assertStatus(204);
    }

    public function test_logout_without_auth_user()
    {
        User::factory()->create();
        $response = $this->post(route('apiLogout'));
        $response->assertUnauthorized();
    }
}
