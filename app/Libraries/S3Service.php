<?php

namespace App\Libraries;

use App\Exceptions\CustomMessages\PetGalleryUploadLimitationException;
use App\Exceptions\ForbiddenException;
use App\Models\Pet;
use App\Models\Report;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
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

    private FilesystemAdapter $s3;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->s3 = Storage::disk('s3');
    }

    /**
     * @throws ForbiddenException
     */
    public function createAvatar(UploadedFile $file, User|Pet $model): User|Pet
    {
        $modelName = get_class($model);
        $this->filePath = config('services.environment').'/'.self::PATH_BEFORE.$this->user->id;
        $this->filePath = match ($modelName) {
            Pet::class => $this->filePath.self::PETS.$model->id.self::PATH_AVATAR,
            User::class => $this->filePath.self::PATH_AVATAR,
            default => throw new ForbiddenException,
        };
        if ($model->avatar) {
            $this->s3Remove($model->avatar['path']);
        }
        $data = $this->upload($file);

        return $this->uploadAvatar($model, $data);
    }

    /**
     * @throws ForbiddenException
     * @throws PetGalleryUploadLimitationException
     */
    public function createGallery(UploadedFile $file, Pet $pet): Pet
    {
        $modelName = get_class($pet);
        $this->filePath = config('services.environment').'/'.self::PATH_BEFORE.$this->user->id;
        if ($pet->gallery && count($pet->gallery) >= Pet::MAX_PET_UPLOADS_LIMIT) {
            throw new PetGalleryUploadLimitationException;
        }
        $this->filePath = match ($modelName) {
            Pet::class => $this->filePath.self::PETS.$pet->id.self::PATH_GALLERY,
            default => throw new ForbiddenException,
        };
        $data = $this->upload($file);

        return $this->uploadGallery($pet, $data);
    }

    /**
     * @return array<string, string>
     */
    public function upload(UploadedFile $file): array
    {
        $this->addFile($file);
        $root = $this->s3->url($this->filePath);
        $rootThumb = $this->s3->url($this->filePathThumb);
        $data = [];
        $data['type'] = self::TYPE_AVATAR;
        $data['name'] = $this->fileName;
        $data['root'] = $root;
        $data['root_thumb'] = $rootThumb;
        $data['path'] = str_replace('https://lost-my-pet.s3.eu-central-1.amazonaws.com/', '', $root);
        $data['path_thumb'] = str_replace('https://lost-my-pet.s3.eu-central-1.amazonaws.com/', '', $rootThumb);

        return $data;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function uploadAvatar(User|Pet $model, array $data): User|Pet
    {
        $model->avatar = $data;
        $model->save();

        return $model;
    }

    /**
     * @param  array<string, string>  $data
     */
    public function uploadGallery(Pet $pet, array $data): Pet
    {
        $gallery = $pet->gallery;
        $gallery[] = $data;
        $pet->gallery = $gallery;
        $pet->save();

        return $pet;
    }

    /**
     * @param  array<string>  $files
     */
    public function removeGallery(Pet $pet, array $files): Pet
    {
        $petGalleries = $pet->gallery;
        $newGallery = [];
        foreach ($files as $file) {
            foreach ($petGalleries as $key => $image) {
                $save = true;
                if ($image['root'] === $file) {
                    $this->s3Remove($image['path']);
                    $save = false;
                    unset($petGalleries[$key]);
                }
                if ($save) {
                    $newGallery[] = $image;
                }
            }
        }
        $pet->gallery = $newGallery;
        $pet->save();

        return $pet;
    }

    public function s3Remove(string $path): bool
    {
        $awsDelete = $this->s3->delete($path);
        if (! $awsDelete) {
            return $awsDelete;
        }

        return true;
    }

    private function addFile(UploadedFile $originalFile): void
    {
        $timeNow = now()->timestamp;
        $s3Path = $this->filePath;
        $fileExtension = $originalFile->getClientOriginalExtension();
        $this->fileName = $timeNow.'.'.$fileExtension;
        $this->fileNameThumb = $timeNow.'-thumb.'.$fileExtension;
        $this->filePath = $s3Path.'/'.$this->fileName;
        $this->filePathThumb = $s3Path.'/'.$this->fileNameThumb;
        $file = Image::make($originalFile);
        $fileThumb = Image::make($originalFile);
        $file = $file->resize(Report::DEFAULT_AVATAR_ORIGIN_IN_PIXEL, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $fileThumb = $fileThumb->resize(Report::DEFAULT_AVATAR_THUMB_IN_PIXEL, null, function ($constraint) {
            $constraint->upsize();
            $constraint->aspectRatio();
        });
        $file->orientate();
        $fileThumb->orientate();
        $this->s3->put($this->filePath, $file->stream(), 'public'); //['visibility' => 'public']
        $this->s3->put($this->filePathThumb, $fileThumb->stream(), 'public'); //['visibility' => 'public']
    }
}
