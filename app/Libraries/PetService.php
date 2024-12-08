<?php

namespace App\Libraries;

use App\Exceptions\ForbiddenException;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class PetService
{
    /** @var Request $request */
    private $request;
    /** @var User $user */
    private $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->user = $request->user();
    }

    /**
     * Create new User Model
     *
     * @return Pet
     * @throws \Throwable
     */
    public function create()
    {
        /** @var Pet $pet */
        $pet = $this->user->pets()->firstOrNew(['id' => null]);
        return $this->update($pet);
    }

    /**
     * @param $pet
     * @return Pet|bool
     * @throws \Throwable
     */
    public function update(Pet $pet)
    {
        $pet->fill($this->request->all());
        $pet->save();
        return $this->updateAvatar($pet);
    }

    /**
     * @param Pet $pet
     * @return Pet|User|bool|mixed
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function updateAvatar(Pet $pet)
    {
        if($this->request->has('file')) {
            $storage = new S3Service($this->user);
            $pet = $storage->createAvatar($this->request->file, $pet);
        }
        return $pet;
    }

    /**
     * @param Pet $pet
     * @return Pet|bool
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function addGallery(Pet $pet)
    {
        if($this->request->has('file')) {
            $storage = new S3Service($this->user);
            $file = $this->request->file;
//            foreach ($this->request->file as $file) {
                $pet = $storage->createGallery($file, $pet);
//            }
        }
        return $pet;
    }

    /**
     * @param Pet $pet
     * @param $files
     * @return mixed
     */
    public function removeGallery(Pet $pet, $files)
    {
        $this->checkUserPet($pet);
        $storage = new S3Service($this->user);
        $pet = $storage->removeGallery($pet, $files);
        return $pet;
    }

    private function checkUserPet(Pet $pet)
    {
//        if($this->user->pets())
//        print_r($this->user->pets()->count());die;
    }
}
