<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPetController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $pets = Pet::orderByDesc('created_at')->with('user')->withTrashed()->paginate($itemsPerPage, ['*'], 'page', $page);
        return response()->json($pets, 200);
    }

    /**
     * @param Request $request
     * @param $reportedPetId
     * @return JsonResponse
     */
    public function show(Request $request, $reportedPetId)
    {
        $pet = Pet::withTrashed()->with('user')->find($reportedPetId);
        return response()->json($pet, 200);
    }
}
