<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $users = User::orderByDesc('created_at')->withTrashed()->paginate($itemsPerPage, ['*'], 'page', $page);
        return response()->json($users, 200);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function show(Request $request, User $user)
    {
        return response()->json($user, 200);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function reportedPets(Request $request, User $user)
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = $user->reportedPets()->orderByDesc('created_at')->with('user')->withTrashed()->paginate($itemsPerPage, ['*'], 'page', $page);
        return response()->json($reportedPets, 200);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function userPets(Request $request, User $user)
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = $user->pets()->orderByDesc('created_at')->with('user')->withTrashed()->paginate($itemsPerPage, ['*'], 'page', $page);
        return response()->json($reportedPets, 200);
    }
}
