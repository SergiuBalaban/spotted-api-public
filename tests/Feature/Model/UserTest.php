<?php

namespace Tests\Feature\Model;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_create_user()
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseHas(User::class, $user->toArray());
        $this->assertModelExists($user);
    }

    public function test_update_user_by_name()
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseHas(User::class, $user->attributesToArray());
        $oldName = $user->name;
        $newName = fake()->name();
        $user->update([
            'name' => $newName,
        ]);
        $user->refresh();
        $this->assertEquals($user->name, $newName);
        $this->assertDatabaseMissing(User::class, ['name' => $oldName]);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $user->delete();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertSoftDeleted($user);
    }
}
