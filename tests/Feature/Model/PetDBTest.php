<?php

namespace Tests\Feature\Model;

use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class PetDBTest extends TestCase
{
    public function test_get_pets()
    {
        Pet::factory()->asNewUser()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);
        $user = User::query()->first();
        $pet = $user->pets()->first();
        $this->assertModelExists($pet);
    }

    public function test_create_pet()
    {
        $pet = Pet::factory()->asNewUser()->create();
        $user = $pet->user;
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertModelExists($pet);
        $this->assertModelExists($user);
    }

    public function test_delete_pet()
    {
        $pet = Pet::factory()->asNewUser()->create();
        $pet->delete();
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(User::class, 1);
        $this->assertSoftDeleted($pet);
    }

    public function test_update_pet_by_nickname()
    {
        $pet1 = Pet::factory()->asNewUser()->create();
        $pet2 = Pet::factory()->asNewUser()->create();
        $this->assertDatabaseCount(Pet::class, 2);
        $this->assertDatabaseCount(User::class, 2);
        $oldNickName = $pet1->nickname;
        $pet1->update([
            'nickname' => $pet2->nickname,
        ]);
        $pet1->refresh();
        $this->assertEquals($pet1->nickname, $pet2->nickname);
        $this->assertDatabaseMissing(Pet::class, ['nickname' => $oldNickName]);
    }

    public function test_update_pet_status_as_missing()
    {
        $pet = Pet::factory()->asNewUser()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);

        $pet->update([
            'status' => Pet::STATUS_MISSING,
        ]);
        $this->assertEquals(Pet::STATUS_MISSING, $pet->status);
    }

    public function test_update_pet_status_as_found()
    {
        $pet = Pet::factory()->asNewUser()->create([
            'status' => Pet::STATUS_MISSING,
        ]);
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);

        $pet->update([
            'status' => Pet::STATUS_FOUND,
        ]);
        $this->assertEquals(Pet::STATUS_FOUND, $pet->status);
    }
}
