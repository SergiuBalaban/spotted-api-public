<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $users = User::query()
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return response()->json($users);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function reportedPets(Request $request, User $user): JsonResponse
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = Report::withTrashed()
            ->where('user_id', $user->id)
            ->where('status', Report::STATUS_REPORTED)
            ->orderByDesc('created_at')
            ->with('user')
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return response()->json($reportedPets);
    }

    public function userPets(Request $request, User $user): JsonResponse
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = $user->pets()
            ->withTrashed()
            ->orderByDesc('created_at')
            ->with('user')
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return response()->json($reportedPets);
    }
}
