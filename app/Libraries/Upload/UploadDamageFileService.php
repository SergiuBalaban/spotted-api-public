<?php

namespace App\Services\Upload;

use App\Collision;
use App\CollisionFile;
use App\Damage;
use App\Exceptions\CustomValidationException;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UploadDamageFileService extends UploadFileService
{
    /** @var Collision $collision */
    private $collision;
    /** @var User $user */
    protected $user;
    private $userRole;

    /**
     * UploadDamageFileService constructor.
     *
     * @param Request $request
     * @param Collision $collision
     * @param $userRole
     * @throws CustomValidationException
     */
    public function __construct(Request $request, Collision $collision, $userRole)
    {
        $this->collision = $collision;
        $this->storagePath = '/collisions/' . $this->collision->id . '/';
        $this->user = $request->user();
        $this->userRole = $userRole;
        parent::__construct($request);
    }

    /**
     * @return array|void
     */
    public function upload()
    {
        $collisionFile = $this->uploadCollisionFile();
        if ($collisionFile) {
            $gallery = [];
            $key = $this->request->get('key');
            $damage = $this->collision->damages()
                ->where('type', $this->request->get('type'))
                ->where('key', $key)
                ->first();
            if (!isset($damage->id)) {
                $damage = new Damage();
            } else {
                $gallery = $damage->gallery;
            }

            $uploadedGallery = $this->getUploadedGallery($collisionFile);
            $gallery[] = $uploadedGallery;
            $damagesConfig = config('damages');
            $params = isset($damagesConfig[$key]) ? $damagesConfig[$key] : $damagesConfig[Damage::KEY_DAMAGES_OTHERS];

            $damage->fill($this->request->all());
            $damage->collision_id = $this->collision->id;
            $damage->title = $params['title'];
            $damage->gallery = $gallery;
            $damage->save();
            return [
                'uploadedGallery' => $uploadedGallery,
                'damage' => $damage,
            ];
        }
        return;
    }

    /**
     * @return CollisionFile|void
     */
    public function uploadCollisionFile()
    {
        $collisionFile = null;
        if ($this->imageUrl && $this->thumbUrl) {
            $collisionFile = $this->createCollisionFile();
        }
        return $collisionFile;
    }

    /**
     * @return CollisionFile
     */
    private function createCollisionFile()
    {
        $image = new CollisionFile();
        $image->collision_id    = $this->collision->id;
        $image->file_url        = $this->imageUrl;
        $image->thumb_url       = $this->thumbUrl;
        $image->extension       = $this->dataImage['file_extension'];
        $image->key             = $this->dataImage['file_key'];
        $image->fileable_type   = Collision::class;
        $image->fileable_id     = $this->collision->id;
        $image->exif_data       = isset($this->dataImage['exifData']) ? $this->dataImage['exifData'] : null;
        $image->save();
        $image->name            = $image->key. '.' .$image->extension;
        $image->created_at      = Carbon::now();
        return $image;
    }

    /**
     * @param $collisionFile
     * @return array
     */
    private function getUploadedGallery($collisionFile)
    {
        return [
            "id"            => $collisionFile->id,
            "key"           => $this->dataImage['file_key'],
            "file_url"      => $this->imageUrl,
            "url"           => $this->thumbUrl,
            "name"          => $this->dataImage['file_name'],
            "size"          => $this->dataImage['file_size'],
            "type"          => $this->dataImage['file_mime'],
            "speed"         => 0,
            "user_id"       => $this->user->id,
            "user_name"     => $this->user->name,
            "user_role"     => $this->userRole,
            "active"        => false,
            "sender"        => Damage::SENDER_GALLERY,
            "success"       => true,
            "timeout"       => 0,
            "progress"      => "100.00",
            "collision_id"  => $this->collision->id,
            "created_at"    => Carbon::now(),
            'exif_data'     => $collisionFile->exif_data
        ];
    }
}
