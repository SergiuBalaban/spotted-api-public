<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function statistics(Request $request)
    {
        $totalUsers = User::all()->count();
        $pets = Pet::all();
        $totalUserPets = $pets->count();
        $totalUserMissingPets = $pets->where('status', ReportedPet::STATUS_MISSING)->count();
        $reportedPets = ReportedPet::all();
        $totalSpottedPets = $reportedPets->where('status', ReportedPet::STATUS_REPORTED)->count();
        $totalFoundedPets = $reportedPets->where('status', ReportedPet::STATUS_FOUND)->count();
        $totalFoundedPetsOnApp = $reportedPets->where('status', ReportedPet::STATUS_FOUND)->where('found_in_app', 1)->count();

        $statistics = [
            'total_users' => $totalUsers,
            'total_user_pets' => $totalUserPets,
            'total_user_missing_pets' => $totalUserMissingPets,
            'total_spotted_pets' => $totalSpottedPets,
            'total_founded_pets' => $totalFoundedPets,
            'total_founded_pets_on_app' => $totalFoundedPetsOnApp,
        ];
        return response()->json($statistics, 200);
    }
}
