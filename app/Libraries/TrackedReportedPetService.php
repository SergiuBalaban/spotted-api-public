<?php

namespace App\Libraries;

use App\Exceptions\UnauthorizedException;
use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\TrackedReportedPet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrackedReportedPetService
{
    /** @var Request $request */
    private $request;
    /** @var User $user */
    private $user;

    /**
     * PetReportService constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->user = $request->user();
    }

    /**
     * @param ReportedPet $reportedPet
     */
    public function addReportedPetToAllMissingPets(ReportedPet $reportedPet)
    {
        $missingPets = Pet::where('status', Pet::STATUS_MISSING)->where('category', $reportedPet->category)->get();
        /** @var Pet $missingPet */
        foreach ($missingPets as $missingPet) {
            if($missingPet->reportedMissingPet->city == $reportedPet->city) {
                $missingPet->trackedReportedPet($reportedPet->id)->withTrashed()->firstOrCreate([
                    'reported_pet_id' => $reportedPet->id,
                    'category' => $missingPet->category,
                ]);
            }
        }
    }

    /**
     * @param Pet $pet
     */
    public function trackThisPet(Pet $pet)
    {
        $trackedReportedPetIds = [];
        $reportedPetsIds = ReportedPet::where('status', ReportedPet::STATUS_REPORTED)
            ->where('category', $pet->category)
            ->where('user_id', '!=', $pet->user_id)
            ->pluck('id')
            ->toArray();
        $petReportsSubscribedIds = $pet->trackedReportedPets()->withTrashed()->whereIn('reported_pet_id', $reportedPetsIds)->pluck('reported_pet_id')->toArray();
        $newTrackedReportedPet = array_diff($reportedPetsIds, $petReportsSubscribedIds);
        if($newTrackedReportedPet) {
            foreach ($newTrackedReportedPet as $reportedPet) {
                $trackedReportedPetId['pet_id'] = $pet->id;
                $trackedReportedPetId['category'] = $pet->category;
                $trackedReportedPetId['reported_pet_id'] = $reportedPet;
                $trackedReportedPetId['created_at'] = Carbon::now();
                $trackedReportedPetIds[] = $trackedReportedPetId;
            }
            $pet->trackedReportedPets()->insert($trackedReportedPetIds);
        }
    }

    /**
     * @param Pet $pet
     */
    public function unTrackThisPet(Pet $pet)
    {
        $pet->trackedReportedPets()->delete();
    }

    /**
     * @param TrackedReportedPet $trackedReportedPet
     * @throws UnauthorizedException
     */
    public function markAsIdentical(TrackedReportedPet $trackedReportedPet)
    {
        $this->checkAuthorization($trackedReportedPet);
        $trackedReportedPet->update([
            'is_identically' => 1,
            'status' => TrackedReportedPet::STATUS_IDENTICALLY,
        ]);
    }

    /**
     * @param TrackedReportedPet $trackedReportedPet
     * @throws UnauthorizedException
     */
    public function markAsNotIdentical(TrackedReportedPet $trackedReportedPet)
    {
        $this->checkAuthorization($trackedReportedPet);
        $trackedReportedPet->update([
            'is_identically' => 0,
            'status' => TrackedReportedPet::STATUS_NOT_IDENTICALLY,
        ]);
    }

    /**
     * @param TrackedReportedPet $trackedReportedPet
     * @throws UnauthorizedException
     */
    private function checkAuthorization(TrackedReportedPet $trackedReportedPet)
    {
        $missingPet = $this->user->missingPets()->where('pet_id', $trackedReportedPet->pet_id)->first();
        if(!isset($missingPet->id)) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @param ReportedPet $reportedPet
     */
    public function unsubscribeReportPet(ReportedPet $reportedPet)
    {
        $missingReportedPet = $this->user->missingPets()->first();
        /** @var TrackedReportedPet $trackedReportedPet */
        $trackedReportedPet = $reportedPet->trackedPet($missingReportedPet->pet_id)->first();
        if(isset($trackedReportedPet->id)) {
            $trackedReportedPet->delete();
        }
    }

    /**
     * @param ReportedPet $reportedPet
     */
    public function subscribeReportPet(ReportedPet $reportedPet)
    {
        $missingReportedPet = $this->user->missingPets()->first();
        $reportedPet->trackedPet($missingReportedPet->pet_id)->update(['status' => TrackedReportedPet::STATUS_IDENTICALLY]);
    }
}
