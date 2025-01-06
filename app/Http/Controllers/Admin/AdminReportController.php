<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Tasks\Report\FindReportByIdTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $itemsPerPage = $request->length ?? 15;
        $page = $request->start ?? 1;
        $reportedPets = Report::query()
            ->orderByDesc('created_at')
            ->with('user')
            ->withTrashed()
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return response()->json($reportedPets);
    }

    public function show(Request $request, int $reportedPetId): JsonResponse
    {
        $reportedPet = Report::withTrashed()->find($reportedPetId);

        return response()->json($reportedPet);
    }

    public function deleteReportedPet(Request $request, int $reportedPetId): Response
    {
        $reportedPet = app(FindReportByIdTask::class)->run($reportedPetId);
        $user = $reportedPet->user;
        if ($user) {
            $reportedPet->user()->update([
                'banned' => true,
                'banned_count' => $user->banned_count + 1,
                'banned_at' => now()->addHour(),
            ]);
            $reportedPet->delete();
        }

        return response()->noContent();
    }
}
