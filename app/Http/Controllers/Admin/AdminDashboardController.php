<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function statistics(Request $request): JsonResponse
    {
        $data = [
            'total_users' => User::query()->count(),
            'total_pets' => Pet::query()->count(),
            'total_reported_pets' => Report::query()->where('status', Report::STATUS_REPORTED)->count(),
            'total_missing_pets' => Report::query()->where('status', Report::STATUS_MISSING)->count(),
        ];

        return response()->json($data);
    }
}
