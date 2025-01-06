<?php

namespace App\Http\Controllers;

use App\Actions\Map\GetMissingPetsAction;
use App\Actions\Map\GetReportsForMissingPetAction;
use App\Http\Requests\MapRequest;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    //    public function index(MapRequest $request): JsonResponse
    //    {
    //        /** @var User $user */
    //        $user = $request->user();
    //        $currentLatitude = $request->get('current_latitude');
    //        $currentLongitude = $request->get('current_longitude');
    //
    //        //TODO: GET all AUTH user missing && reported pets
    //        $authUserReportedPetsCollection = $user->allReportedPets()
    //            ->whereIn('status', [Report::STATUS_REPORTED, Report::STATUS_MISSING])
    //            ->get()
    //            ->map(function (Report $reportedPet) {
    //                $reportedPet->is_subscribed = false;
    //                $reportedPet->track_id = null;
    //                $reportedPet->track_status = '';
    //                $reportedPet->can_edit = $reportedPet->status === Report::STATUS_REPORTED;
    //
    //                return $reportedPet;
    //            })
    //            ->toArray();
    //
    //        //TODO: GET all missing pets from current city
    //        $reportedMissingPetsAsCollection = Report::where('status', Report::STATUS_MISSING)
    //            ->where('user_id', '!=', $user->id);
    //        $reportedMissingPetsAsCollection = getReportedPetsByLocation($reportedMissingPetsAsCollection, $currentLatitude, $currentLongitude);
    //        $reportedMissingPetsAsCollection = $reportedMissingPetsAsCollection
    //            ->get()
    //            ->map(function (Report $reportedPet) {
    //                $reportedPet->is_subscribed = false;
    //                $reportedPet->track_id = null;
    //                $reportedPet->track_status = '';
    //                $reportedPet->can_edit = false;
    //
    //                return $reportedPet;
    //            })
    //            ->toArray();
    //
    //        //TODO: GET all reported pets from current city from specific category if user have a missing pet
    //        $reportedPetsAsCollection = [];
    //        /** @var Report $userMissingPet */
    //        $userReportedMissingPet = $user->missingReportedPets()->first();
    //        if (isset($userReportedMissingPet->id)) {
    //            $reportedPetsAsCollection = Report::query()
    //                ->where('status', Report::STATUS_REPORTED)
    //                ->where('user_id', '!=', $user->id)
    //                ->where('category', $userReportedMissingPet->category);
    //            $reportedPetsAsCollection = getReportedPetsByLocation($reportedPetsAsCollection, $currentLatitude, $currentLongitude);
    //            $reportedPetsAsCollection = $reportedPetsAsCollection
    //                ->get()
    //                ->map(function (Report $reportedPet) use ($userReportedMissingPet) {
    //                    $reportedPet->can_edit = false;
    //                    $reportedPetTracked = $reportedPet->trackedPet($userReportedMissingPet->pet_id)->first();
    ////                    $reportedPet->is_subscribed = isset($reportedPetTracked->id) && $reportedPetTracked->status !== TrackedReportedPet::STATUS_NOT_IDENTICALLY;
    //                    $reportedPet->track_id = $reportedPetTracked->id ?? null;
    //                    $reportedPet->track_status = $reportedPetTracked->status ?? '';
    //
    //                    return $reportedPet;
    //                })
    //                ->toArray();
    //        }
    //
    //        $reportedPets = array_merge($authUserReportedPetsCollection, $reportedMissingPetsAsCollection, $reportedPetsAsCollection);
    //        usort($reportedPets, function ($a, $b) {
    //            return $a['id'] < $b['id'];
    //        });
    //
    //        return response()->json($reportedPets);
    //    }

    public function getMissingPets(MapRequest $request): JsonResponse
    {
        $missingPets = app(GetMissingPetsAction::class)->run($request);

        return response()->json($missingPets);
    }

    public function getReportsForMissingPet(MapRequest $request): JsonResponse
    {
        $missingPets = app(GetReportsForMissingPetAction::class)->run($request);

        return response()->json($missingPets);
    }
}
