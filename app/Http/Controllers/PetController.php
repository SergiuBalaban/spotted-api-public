<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\OnePetCreationException;
use App\Http\Requests\PetCreateRequest;
use App\Http\Requests\PetRemoveGalleryRequest;
use App\Http\Requests\PetRequest;
use App\Libraries\PetService;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $pets = $user->pets()->get();
        return response()->json($pets, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PetCreateRequest $request
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function store(PetCreateRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        if($user->pets()->count() >= 1) {
            throw new OnePetCreationException();
        }
        $petService = new PetService($request);
        $pet = $petService->create();

        return response()->json($pet, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Pet $pet
     * @return JsonResponse
     */
    public function show(Request $request, Pet $pet)
    {
        return response()->json($pet, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PetRequest $request
     * @param Pet $pet
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(PetRequest $request, Pet $pet)
    {
        $petService = new PetService($request);
        $pet = $petService->update($pet);
        return response()->json($pet, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Pet $pet
     * @return JsonResponse
     */
    public function destroy(Request $request, Pet $pet)
    {
        $pet->delete();
        return response()->json(['success' => 1], 200);
    }

    /**
     * @param Request $request
     * @param Pet $pet
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function addGallery(Request $request, Pet $pet)
    {
        $petService = new PetService($request);
        $pet = $petService->addGallery($pet);
        return response()->json($pet, 200);
    }

    /**
     * @param PetRemoveGalleryRequest $request
     * @param Pet $pet
     * @return JsonResponse
     */
    public function removeGallery(PetRemoveGalleryRequest $request, Pet $pet)
    {
        $petService = new PetService($request);
        $pet = $petService->removeGallery($pet, $request->get('files'));
        return response()->json($pet, 200);
    }

    /**
     * @param Request $request
     * @param Pet $pet
     * @return JsonResponse
     */
    public function trackedReportedPets(Request $request, Pet $pet)
    {
        return response()->json($pet->trackedReportedPets()->get(), 200);
    }
}
