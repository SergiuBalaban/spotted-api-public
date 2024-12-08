<?php

namespace App\Libraries;

use App\Exceptions\ForbiddenException;
use App\Exceptions\PetGalleryUploadLimitationException;
use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class S3Service
{
    const PATH_BEFORE = 'users/';
    const PETS = '/pets/';
    const PET_REPORTED = '/reportedPets';
    const DEFAULT = '/default';
    const PATH_AVATAR = '/avatar';
    const PATH_GALLERY = '/gallery';

    const TYPE_AVATAR = 'avatar';
    const TYPE_GALLERY = 'gallery';

    private string $filePath;
    private string $filePathThumb;
    private string $fileName;
    private string $fileNameThumb;
    private $s3;
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->s3 = Storage::disk('s3');
    }

    /**
     * @param $file
     * @param $model
     * @return Pet|User|bool|mixed
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function createAvatar($file, $model)
    {
        $this->filePath = config('services.environment').'/'.self::PATH_BEFORE.$this->user->id;
        switch ($model) {
            case ($model instanceof Pet):
                $this->filePath = $this->filePath.self::PETS.$model->id.self::PATH_AVATAR;
                break;
            case ($model instanceof User):
                $this->filePath = $this->filePath.self::PATH_AVATAR;
                break;
            case ($model instanceof ReportedPet):
                $this->filePath = $this->filePath.self::PET_REPORTED;
                break;
            default:
                throw new ForbiddenException();
        }
        if($model->avatar) {
            $this->s3Remove($model->avatar['path']);
        }
        $data = $this->upload($file);
        return $this->uploadAvatar($model, $data);
    }

    /**
     * @param $file
     * @param $model
     * @return bool
     * @throws ForbiddenException
     * @throws \Throwable
     */
    public function createGallery($file, $model)
    {
        $this->filePath = config('services.environment').'/'.self::PATH_BEFORE.$this->user->id;
        if($model->gallery && count($model->gallery) >= Pet::MAX_PET_UPLOADS_LIMIT) {
            throw new PetGalleryUploadLimitationException();
        }
        switch ($model) {
            case ($model instanceof Pet):
                $this->filePath = $this->filePath.self::PETS.$model->id.self::PATH_GALLERY;
                break;
            default:
                throw new ForbiddenException();
        }
        $data = $this->upload($file);
        return $this->uploadGallery($model, $data);
    }

    /**
     * @param $file
     * @return array|bool
     * @throws \Throwable
     */
    public function upload($file)
    {
        $this->addFile($file);
        $root = $this->s3->url($this->filePath);
        $rootThumb = $this->s3->url($this->filePathThumb);
        $data = [];
        $data['type'] = self::TYPE_AVATAR;
        $data['name'] = $this->fileName;
        $data['root'] = $root;
        $data['root_thumb'] = $rootThumb;
        $data['path'] = str_replace("https://lost-my-pet.s3.eu-central-1.amazonaws.com/", "", $root);
        $data['path_thumb'] = str_replace("https://lost-my-pet.s3.eu-central-1.amazonaws.com/", "", $rootThumb);
        return $data;
    }

    /**
     * @param $model
     * @param $data
     * @return mixed
     */
    public function uploadAvatar($model, $data)
    {
        $model->avatar = $data;
        $model->save();
        return $model;
    }

    /**
     * @param $model
     * @param $data
     * @return mixed
     */
    public function uploadGallery($model, $data)
    {
        $gallery = $model->gallery;
        $gallery[] = $data;
        $model->gallery = $gallery;
        $model->save();
        return $model;
    }

    /**
     * @param $model
     * @param $files
     * @return mixed
     */
    public function removeGallery($model, $files)
    {
        $petGalleries = $model->gallery;
        $newGallery = [];
        foreach ($files as $file) {
            foreach ((array)$petGalleries as $key => $image) {
                $save = true;
                if ($image['root'] === $file) {
                    $this->s3Remove($image['path']);
                    $save = false;
                    unset($petGalleries[$key]);
                }
                if($save) {
                    $newGallery[] = $image;
                }
            }
        }
        $model->gallery = $newGallery;
        $model->save();
        return $model;
    }

    /**
     * @param $path
     * @return bool
     */
    public function s3Remove($path)
    {
        $awsDelete = $this->s3->delete($path);
        if(!$awsDelete) {
            return $awsDelete;
        }
        return true;
    }

    /**
     * @param $originalFile
     */
    private function addFile($originalFile)
    {
        $timeNow = Carbon::now()->timestamp;
        $s3Path = $this->filePath;
        $fileExtension = $originalFile->getClientOriginalExtension();
        $this->fileName = $timeNow.'.'.$fileExtension;
        $this->fileNameThumb = $timeNow.'-thumb.'.$fileExtension;
        $this->filePath = $s3Path.'/'.$this->fileName;
        $this->filePathThumb = $s3Path.'/'.$this->fileNameThumb;
        $file = Image::make($originalFile);
        $fileThumb = Image::make($originalFile);
        $file = $file->resize(ReportedPet::DEFAULT_AVATAR_ORIGIN_IN_PIXEL, null, function($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $fileThumb = $fileThumb->resize(ReportedPet::DEFAULT_AVATAR_THUMB_IN_PIXEL, null, function($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $file->orientate();
        $fileThumb->orientate();
        $this->s3->put($this->filePath, $file->stream(), 'public'); //['visibility' => 'public']
        $this->s3->put($this->filePathThumb, $fileThumb->stream(), 'public'); //['visibility' => 'public']
    }
}
