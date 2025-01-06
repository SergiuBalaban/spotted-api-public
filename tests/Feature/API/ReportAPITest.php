<?php

namespace Tests\Feature\API;

use App\Events\ReportCreatedEvent;
use App\Events\ReportDeletedEvent;
use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Models\Chat;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReportAPITest extends TestCase
{
    // POST reportedPet
    public function test_can_post_new_report()
    {
        //Given
        $user = User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, 0);

        //When
        $payload = Report::factory()->make()->toArray();
        $payload['address_line'] = $payload['formatted_address'];
        $response = $this->actingAs($user)->postJson(route('report.apiCreateReportedPet'), $payload);

        //Then
        $responseData = $response->assertOk()->json();
        $report = Report::first();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, 1);
        $this->assertEquals($user->id, $report->user_id);
        $status = Report::STATUS_REPORTED;
        $this->assertDatabaseHas(Report::class, [
            'id' => $report->id,
            'user_id' => $report->user_id,
            'country' => $report->country,
            'city' => $report->city,
            'longitude' => $report->longitude,
            'latitude' => $report->latitude,
            'formatted_address' => $report->formatted_address,
            'category' => $report->category,
            'message' => $report->message,
            'status' => $report->status,
            'pet_id' => null,
        ]);
        $this->assertEquals($responseData['status'], $status);
        $this->assertEquals($report->status, $status);
        $this->assertEquals($report->category, $report->category);
        $this->assertEquals($report->latitude, $report->latitude);
        $this->assertEquals($report->longitude, $report->longitude);
        $this->assertEquals($report->country, $report->country);
        $this->assertEquals($report->city, $report->city);
        $this->assertEquals($report->formatted_address, $report->formatted_address);
        $this->assertEquals($report->message, $report->message);
    }

    public function test_cannot_post_new_report_with_already_existing_report_from_today()
    {
        //Given
        $user = User::factory()->create();
        $this->actingAs($user);
        Report::factory(Report::DEFAULT_USER_REPORTED_PETS)->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS);

        //When
        $payload = Report::factory()->make()->toArray();
        $payload['address_line'] = $payload['formatted_address'];
        $response = $this->post(route('report.apiCreateReportedPet'), $payload);

        //Then
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_MAX_REPORTS_MESSAGE, ErrorMessageValue::ERROR_MAX_REPORTS_CODE);
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS);
    }

    public function test_can_post_new_report_with_already_existing_reports_from_yesterday()
    {
        //Given
        $user = User::factory()->create();
        $this->actingAs($user);
        Report::factory(Report::DEFAULT_USER_REPORTED_PETS - 1)->create([
            'created_at' => now()->addDays(2),
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS - 1);

        //When
        $payload = Report::factory()->make()->toArray();
        $payload['address_line'] = $payload['formatted_address'];
        $response = $this->post(route('report.apiCreateReportedPet'), $payload);

        //Then
        $response->assertOk()->json();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS);
    }

    public function test_can_post_new_report_with_existing_pet()
    {
        //Given
        $user = User::factory()->create();
        Report::factory()->asUser($user)->markedAs()->withPet()->create();
        Report::factory(Report::DEFAULT_USER_REPORTED_PETS - 1)->asUser($user)->create();

        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS);

        //When
        $payload = Report::factory()->make()->toArray();
        $payload['address_line'] = $payload['formatted_address'];
        $response = $this->actingAs($user)->post(route('report.apiCreateReportedPet'), $payload);

        //Then
        $response->assertOk()->json();
        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS + 1);
    }

    public function test_cannot_post_new_report_with_existing_pet()
    {
        //Given
        $user = User::factory()->create();
        Report::factory()->asUser($user)->markedAs()->withPet()->create();
        Report::factory(Report::DEFAULT_USER_REPORTED_PETS)->asUser($user)->create();
        $this->actingAs($user);

        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS + 1);

        //When
        $payload = Report::factory()->make()->toArray();
        $payload['address_line'] = $payload['formatted_address'];
        $response = $this->post(route('report.apiCreateReportedPet'), $payload);

        //Then
        $responseData = $response->assertStatus(403)->json();
        $this->checkFailedResponseData($responseData, ErrorMessageValue::ERROR_MAX_REPORTS_MESSAGE, ErrorMessageValue::ERROR_MAX_REPORTS_CODE);
        $this->assertDatabaseCount(Pet::class, 1);
        $this->assertDatabaseCount(Report::class, Report::DEFAULT_USER_REPORTED_PETS + 1);
    }

    public function test_trigger_job_when_create_report()
    {
        //Given
        $chat = Chat::factory()->start()->create();

        //When
        Event::fake([
            ReportCreatedEvent::class,
        ]);
        $reportMissing = $chat->reportMissing;
        $payload = Report::factory()->make([
            'category' => $reportMissing->category,
            'country' => $reportMissing->country,
            'city' => $reportMissing->city,
        ])->toArray();
        $response = $this->actingAs($chat->owner)->postJson(route('report.apiCreateReportedPet'), $payload);

        //Then
        $responseData = $response->assertOk()->json();
        $this->assertDatabaseCount(Chat::class, 2);
        $this->assertDatabaseHas(Chat::class, [
            'report_found_id' => $responseData['id'],
            'report_missing_id' => $chat->report_missing_id,
        ]);
        Event::assertDispatched(ReportCreatedEvent::class, 1);
    }

    // DELETE reportedPet
    public function test_cannot_delete_other_report()
    {
        //Given
        $report = Report::factory()->asNewUser()->create();
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, 1);

        //When
        $response = $this->deleteJson(route('report.apiDeleteReportedPet', [
            'report' => $report->id,
        ]));
        $response->assertStatus(422);
        $this->assertDatabaseCount(Pet::class, 0);
        $this->assertDatabaseCount(Report::class, 1);
    }

    public function test_can_delete_report()
    {
        //Given
        $report = Report::factory()->asNewUser()->create();
        $user = $report->user;

        $this->assertDatabaseCount(Report::class, 1);

        //When
        $response = $this->actingAs($user)->deleteJson(route('report.apiDeleteReportedPet', [
            'report' => $report->id,
        ]));

        //Then
        $response->assertNoContent();
        $report->refresh();
        $this->assertSoftDeleted($report);
        $this->assertDatabaseCount(Report::class, 1);
    }

    public function test_trigger_job_when_delete_report()
    {
        //Given
        Event::fake([
            ReportDeletedEvent::class,
        ]);
        $chat = Chat::factory()->start()->create();

        //When
        $response = $this->actingAs($chat->reporter)->deleteJson(route('report.apiDeleteReportedPet', ['report' => $chat->reportFound]));

        //Then
        $response->assertNoContent();
        $this->assertDatabaseCount(Chat::class, 1);
        $this->assertDatabaseHas(Chat::class, [
            'id' => $chat->id,
            'report_found_id' => $chat->report_found_id,
            'active' => 0,
        ]);
        Event::assertDispatched(ReportDeletedEvent::class, 1);
    }
}
