<?php

namespace App\Http\Controllers;

use App\Exceptions\UnauthorizedException;
use App\Libraries\TrackedReportedPetService;
use App\Models\TrackedReportedPet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackedReportedPetController extends Controller
{
    /**
     * @param Request $request
     * @param TrackedReportedPet $trackedReportedPet
     * @return JsonResponse
     * @throws UnauthorizedException
     */
    public function markAsIdentical(Request $request, TrackedReportedPet $trackedReportedPet)
    {
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->markAsIdentical($trackedReportedPet);
        return response()->json($trackedReportedPet->refresh(), 200);
    }

    /**
     * @param Request $request
     * @param TrackedReportedPet $trackedReportedPet
     * @return Response
     * @throws UnauthorizedException
     */
    public function markAsNotIdentical(Request $request, TrackedReportedPet $trackedReportedPet)
    {
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->markAsNotIdentical($trackedReportedPet);
        return response()->noContent();
    }
}
