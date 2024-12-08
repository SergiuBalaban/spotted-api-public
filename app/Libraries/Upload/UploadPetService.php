<?php

namespace App\Libraries\Upload;

use App\Exceptions\CustomValidationException;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class UploadPetService extends UploadFileService
{
    /** @var User $user */
    protected $user;
    protected $pet;

    /**
     * UploadDamageFileService constructor.
     *
     * UploadPetService constructor.
     * @param Request $request
     * @param Pet $pet
     * @throws CustomValidationException
     */
    public function __construct(Request $request, Pet $pet)
    {
        $this->pet = $pet;
        $this->storagePath = '/pets/avatar' . $this->pet->id . '/';
        $this->user = $request->user();
        parent::__construct($request);
    }

    /**
     * @return Pet
     */
    public function upload()
    {
        $data['type'] = self::TYPE_AVATAR;
        $data['name'] = $this->dataImage['file_name'];
        $data['root'] = $this->imageUrl;
        $data['path'] = str_replace("https://lost-my-pet.s3.eu-central-1.amazonaws.com/", "", $this->imageUrl);
        $this->pet->avatar = $data;
        $this->pet->save();
        return $this->pet;
    }
}
