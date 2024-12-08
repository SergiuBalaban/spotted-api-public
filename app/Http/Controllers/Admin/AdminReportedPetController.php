<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportedPet;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportedPetController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = ReportedPet::orderByDesc('created_at')->with('user')->withTrashed()->paginate($itemsPerPage, ['*'], 'page', $page);
        return response()->json($reportedPets, 200);
    }

    /**
     * @param Request $request
     * @param $reportedPetId
     * @return JsonResponse
     */
    public function show(Request $request, $reportedPetId)
    {
        $reportedPet = ReportedPet::withTrashed()->find($reportedPetId);
        return response()->json($reportedPet, 200);
    }

    /**
     * @param Request $request
     * @param $reportedPetId
     * @return JsonResponse
     */
    public function deleteReportedPet(Request $request, $reportedPetId)
    {
        $reportedPet = ReportedPet::withTrashed()->find($reportedPetId);
        $user = $reportedPet->user;
        $reportedPet->user()->update([
            'banned' => true,
            'banned_count' => $user->banned_count + 1,
            'banned_at' => Carbon::now()->addHour(),
        ]);
        $reportedPet->delete();
        return response()->json([], 200);
    }
}
