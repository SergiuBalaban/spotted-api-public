<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function statistics(Request $request)
    {
        $data = [
            'total_users' => User::all()->count(),
            'total_pets' => Pet::all()->count(),
            'total_reported_pets' => ReportedPet::where('status', ReportedPet::STATUS_REPORTED)->count(),
            'total_missing_pets' => ReportedPet::where('status', ReportedPet::STATUS_MISSING)->count(),
        ];
        return response()->json($data, 200);
    }
}
