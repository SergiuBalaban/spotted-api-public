<?php

namespace App\Http\Controllers;

use App\Http\Requests\MapRequest;
use App\Models\ReportedPet;
use App\Models\TrackedReportedPet;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param MapRequest $request
     * @return JsonResponse
     */
    public function index(MapRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $currentLatitude = $request->get('current_latitude');
        $currentLongitude = $request->get('current_longitude');

        //TODO: GET all AUTH user missing && reported pets
        $authUserReportedPetsCollection = $user->allReportedPets()
            ->whereIn('status', [ReportedPet::STATUS_REPORTED, ReportedPet::STATUS_MISSING])
            ->get()
            ->map(function (ReportedPet $reportedPet) {
                $reportedPet->is_subscribed = false;
                $reportedPet->track_id = null;
                $reportedPet->track_status = '';
                $reportedPet->can_edit = $reportedPet->status === ReportedPet::STATUS_REPORTED;
                return $reportedPet;
            })
            ->toArray();

        //TODO: GET all missing pets from current city
        $reportedMissingPetsAsCollection = ReportedPet::where('status', ReportedPet::STATUS_MISSING)
            ->where('user_id', '!=', $user->id);
        $reportedMissingPetsAsCollection = getReportedPetsByLocation($reportedMissingPetsAsCollection, $currentLatitude, $currentLongitude);
        $reportedMissingPetsAsCollection = $reportedMissingPetsAsCollection
            ->get()
            ->map(function (ReportedPet $reportedPet) {
                $reportedPet->is_subscribed = false;
                $reportedPet->track_id = null;
                $reportedPet->track_status = '';
                $reportedPet->can_edit = false;
                return $reportedPet;
            })
            ->toArray();

        //TODO: GET all reported pets from current city from specific category if user have a missing pet
        $reportedPetsAsCollection = [];
        /** @var ReportedPet $userMissingPet */
        $userReportedMissingPet = $user->missingPets()->first();
        if(isset($userReportedMissingPet->id)) {
            $reportedPetsAsCollection = ReportedPet::where('status', ReportedPet::STATUS_REPORTED)
                ->where('user_id', '!=', $user->id)
                ->where('category', $userReportedMissingPet->category);
            $reportedPetsAsCollection = getReportedPetsByLocation($reportedPetsAsCollection, $currentLatitude, $currentLongitude);
            $reportedPetsAsCollection = $reportedPetsAsCollection
                ->get()
                ->map(function (ReportedPet $reportedPet) use ($userReportedMissingPet, $user) {
                    $reportedPet->can_edit = false;
                    $reportedPetTracked = $reportedPet->trackedPet($userReportedMissingPet->pet_id)->first();
                    $reportedPet->is_subscribed = isset($reportedPetTracked->id) && $reportedPetTracked->status !== TrackedReportedPet::STATUS_NOT_IDENTICALLY;
                    $reportedPet->track_id = $reportedPetTracked->id ?? null;
                    $reportedPet->track_status = $reportedPetTracked->status ?? '';
                    return $reportedPet;
                })
                ->toArray();
        }

        $reportedPets = array_merge($authUserReportedPetsCollection, $reportedMissingPetsAsCollection, $reportedPetsAsCollection);
        usort($reportedPets, function ($a, $b) {
            return $a['id'] < $b['id'];
        });

        return response()->json($reportedPets, 200);
    }
}
