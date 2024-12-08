<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\PetNotMissingException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\PetReportRequest;
use App\Libraries\PetReportService;
use App\Libraries\TrackedReportedPetService;
use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PetReportController extends Controller
{
    /**
     * Create new lost pet
     *
     * @param PetReportRequest $request
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function reportedPet(PetReportRequest $request)
    {
        $reportedPetService = new PetReportService($request);
        $reportedPet = $reportedPetService->report();
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->addReportedPetToAllMissingPets($reportedPet);
        return response()->json($reportedPet, 200);
    }

    /**
     * @param PetReportRequest $request
     * @param Pet $pet
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function missingPet(PetReportRequest $request, Pet $pet)
    {
        $petReportService = new PetReportService($request);
        $petReport = $petReportService->report($pet);
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->trackThisPet($pet);
        return response()->json($petReport, 200);
    }

    /**
     * @param PetReportRequest $request
     * @param Pet $pet
     * @return Response
     * @throws PetNotMissingException
     */
    public function foundedPet(PetReportRequest $request, Pet $pet)
    {
        if($pet->status !== Pet::STATUS_MISSING) {
            throw new PetNotMissingException();
        }
        $petReportService = new PetReportService($request);
        $petReportService->found($pet);
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->unTrackThisPet($pet);
        return response()->noContent();
    }

    /**
     * @param Request $request
     * @param ReportedPet $reportedPet
     * @return Response
     */
    public function removeReportedPet(Request $request, ReportedPet $reportedPet)
    {
        /** @var User $user */
        $user = $request->user();
        try {
            $user->reportedPets()->findOrFail($reportedPet->id);
        }catch (\Exception $e) {
            throw new ResourceNotFoundException(ReportedPet::class);
        }
        $reportedPet->delete();
        return response()->noContent();
    }

    /**
     * @param Request $request
     * @param ReportedPet $reportedPet
     * @return Response
     */
    public function unsubscribe(Request $request, ReportedPet $reportedPet)
    {
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->unsubscribeReportPet($reportedPet);
        return response()->noContent();
    }

    /**
     * @param Request $request
     * @param ReportedPet $reportedPet
     * @return Response
     */
    public function subscribe(Request $request, ReportedPet $reportedPet)
    {
        $trackedReportedPetService = new TrackedReportedPetService($request);
        $trackedReportedPetService->subscribeReportPet($reportedPet);
        return response()->noContent();
    }
}
