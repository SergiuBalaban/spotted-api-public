<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $pets = Pet::query()
            ->orderByDesc('created_at')
            ->with('user')
            ->withTrashed()
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return response()->json($pets);
    }

    public function show(Request $request, int $reportedPetId): JsonResponse
    {
        $pet = Pet::withTrashed()->with('user')->find($reportedPetId);

        return response()->json($pet);
    }
}
