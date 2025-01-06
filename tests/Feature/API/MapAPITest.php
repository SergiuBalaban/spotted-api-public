<?php

namespace Tests\Feature\API;

use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MapAPITest extends TestCase
{
    private string $city;

    protected function setUp(): void
    {
        $this->city = fake()->city();
        parent::setUp();
    }

    // GET getMissingPets
    public function test_user_cannot_get_missing_pets_if_not_authenticated()
    {
        //When
        $response = $this->callGetMissingPetsEndpoint();

        //Then
        $response->assertUnauthorized();
    }

    public function test_user_can_get_0_missing_pets()
    {
        //Given
        $user = User::factory()->create();

        //When
        $response = $this->callGetMissingPetsEndpoint($user);

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_user_can_get_0_missing_pets_with_normal_pet()
    {
        //Given
        $pet = Pet::factory()->asNewUser()->create();

        //When
        $city = fake()->city();
        $response = $this->callGetMissingPetsEndpoint($pet->user);

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_user_can_get_0_missing_pets_with_found_pet()
    {
        //Given
        $report = Report::factory()->asNewUser()->markedAs(Report::STATUS_FOUND)->withPet()->create(['city' => $this->city]);

        //When
        $response = $this->callGetMissingPetsEndpoint($report->user);

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_user_can_get_missing_pets()
    {
        //Given
        $randNb = rand(1, 10);
        $reports = Report::factory($randNb)->asNewUser()->markedAs()->withPet()->create(['city' => $this->city]);

        //When
        $response = $this->callGetMissingPetsEndpoint($reports[0]->user);

        //Then
        $response->assertOk()->assertJsonCount($randNb);
    }

    // GET getReportsForMissingPet
    public function test_user_cannot_get_reports_for_missing_pet_if_not_authenticated()
    {
        //When
        $response = $this->callGetReportsForMissingPetEndpoint();

        //Then
        $response->assertUnauthorized();
    }

    public function test_user_can_get_0_reports_for_no_missing_pet()
    {
        //Given
        $report = Report::factory()->asNewUser()->create(['city' => $this->city]);

        //When
        $response = $this->callGetReportsForMissingPetEndpoint($report->user);

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_user_can_get_0_reports_for_missing_pet()
    {
        //Given
        $report = Report::factory()->asNewUser()->markedAs()->withPet()->create(['city' => $this->city]);

        //When
        $response = $this->callGetReportsForMissingPetEndpoint($report->user);

        //Then
        $response->assertOk()->assertJsonCount(0);
    }

    public function test_user_can_get_1_reports_for_missing_pet()
    {
        //Given
        $reportMissingPet = Report::factory()->asNewUser()->markedAs()->withPet()->create(['city' => $this->city]);
        Report::factory(['category' => $reportMissingPet->category])->asNewUser()->create(['city' => $this->city]);

        //When
        $response = $this->callGetReportsForMissingPetEndpoint($reportMissingPet->user);

        //Then
        $response->assertOk()->assertJsonCount(1);
    }

    private function callGetMissingPetsEndpoint(?User $user = null): TestResponse
    {
        if ($user) {
            $this->actingAs($user);
        }

        return $this->getJson(route('dashboard.apiGetMissingPets').'?city='.$this->city);
    }

    private function callGetReportsForMissingPetEndpoint(?User $user = null): TestResponse
    {
        if ($user) {
            $this->actingAs($user);
        }

        return $this->getJson(route('dashboard.apiGetReportsForMissingPets').'?city='.$this->city);
    }
}
