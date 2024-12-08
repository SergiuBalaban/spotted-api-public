<?php

namespace App\Libraries;

use App\Exceptions\ForbiddenException;
use App\Exceptions\MaximumAmountOfReportsException;
use App\Exceptions\PetMissingException;
use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use App\Traits\CoordinationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PetReportService
{
    use CoordinationTrait;

    private Request $request;
    private User $user;

    /**
     * PetReportService constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
        $this->user = $request->user();
    }

    /**
     * @param Pet|null $pet
     * @return ReportedPet
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function report(Pet $pet = null)
    {
        $yesterday = Carbon::now()->subDay();
        $userReports = $this->user->reportedPets()->where('created_at', '>', $yesterday)->count();

        if($userReports > ReportedPet::DEFAULT_USER_REPORTED_PETS) {
            throw new MaximumAmountOfReportsException();
        }

        $status = ReportedPet::STATUS_REPORTED;

        if($pet) {
            if($pet->reportedMissingPet()->first()) {
                throw new PetMissingException();
            }
            $pet->update(['status' => Pet::STATUS_MISSING]);
            $status = ReportedPet::STATUS_MISSING;
        }

        $petReport = new ReportedPet();
        $reportedPetPayload = $this->getFilledDataFromRequest((new ReportedPet())->getFillable(), $this->request);
        $reportedPetPayload = $this->parseCoordinates($reportedPetPayload);
        //TODO: Uncomment only if want to restrict user to report more pets to the same location
//        if(isset($reportedPetPayload['formatted_address'])) {
//            $previousReportedPetWithSameLocation = $this->user->reportedPets()->where('formatted_address', $reportedPetPayload['formatted_address'])->first();
//            if (isset($previousReportedPetWithSameLocation->id)) {
//                throw new ReportedPetSameLocationException();
//            }
//        }
        $petReport->fill($reportedPetPayload);
        $petReport->pet_id = $pet ? $pet->id : null;
        $petReport->user_id = $this->user->id;
        $petReport->status = $status;
        $details = $this->request->get('details');
        $petReport->details = is_string($details) ? json_decode($details) : $details;
        $petReport->save();

        if($this->request->has('file')) {
            $storage = new S3Service($this->user);
            $petReport = $storage->createAvatar($this->request->file, $petReport);
        }
        return $petReport;
    }

    /**
     * @param $fields
     * @param $requestData
     * @return array
     */
    function getFilledDataFromRequest($fields, $requestData)
    {
        $data = [];
        foreach ($fields as $key) {
            if(isset($requestData[$key])) {
                $data[$key] = $requestData[$key];
            }
        }
        return $data;
    }

    /**
     * @param Pet $pet
     * @return Pet
     */
    public function found(Pet $pet)
    {
        $status = Pet::STATUS_FOUND;
        $data = [
            'status' => $status,
            'found_in_app' => $this->request->has(ReportedPet::FOUND_ON_APP) ? $this->request->get(ReportedPet::FOUND_ON_APP) : false
        ];
        $pet->reportedMissingPet()->update($data);
        $pet->update(['status' => $status]);
        return $pet;
    }
}
