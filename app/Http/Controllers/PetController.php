<?php

namespace App\Http\Controllers;

use App\Actions\Pet\CreatePetAction;
use App\Actions\Pet\GetPetsAction;
use App\Actions\Pet\UpdatePetAction;
use App\Actions\Pet\UpdatePetStatusAction;
use App\Actions\Services\DeleteGalleryAction;
use App\Actions\Services\ParseGalleryAction;
use App\Http\Requests\Pet\CreateGalleryPetRequest;
use App\Http\Requests\Pet\CreatePetRequest;
use App\Http\Requests\Pet\DeletePetGalleryRequest;
use App\Http\Requests\Pet\UpdatePetRequest;
use App\Http\Requests\Pet\UpdatePetStatusRequest;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PetController extends Controller
{
    public function getPets(Request $request): JsonResponse
    {
        $pets = app(GetPetsAction::class)->run($request);

        return response()->json($pets);
    }

    public function createPet(CreatePetRequest $request): JsonResponse
    {
        $pet = app(CreatePetAction::class)->run($request);

        return response()->json($pet);
    }

    public function findPetById(Request $request, Pet $pet): JsonResponse
    {
        return response()->json($pet);
    }

    public function updatePet(UpdatePetRequest $request, Pet $pet): JsonResponse
    {
        $pet = app(UpdatePetAction::class)->run($request, $pet);

        return response()->json($pet);
    }

    public function deletePet(Request $request, Pet $pet): Response
    {
        $pet->delete();

        return response()->noContent();
    }

    public function updatePetStatus(UpdatePetStatusRequest $request, Pet $pet): Response
    {
        app(UpdatePetStatusAction::class)->run($request, $pet);

        return response()->noContent();
    }

    public function createPetGallery(CreateGalleryPetRequest $request, Pet $pet): JsonResponse
    {
        $pet = app(ParseGalleryAction::class)->run($request, $pet);

        return response()->json($pet);
    }

    public function deletePetGallery(DeletePetGalleryRequest $request, Pet $pet): JsonResponse
    {
        $pet = app(DeleteGalleryAction::class)->run($request, $pet);

        return response()->json($pet);
    }
}
