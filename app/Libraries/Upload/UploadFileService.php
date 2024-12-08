<?php

namespace App\Libraries\Upload;

use App\Exceptions\CustomValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

abstract class UploadFileService
{
    const ACCESS_PUBLIC = 'public';
    const ACCESS_PRIVATE = 'private';

    const TYPE_CHAT_CHANNEL = 'chat_channel';
    const TYPE_CHAT_MESSAGE = 'chat_message';
    const TYPE_COLLISION = 'collision';
    const TYPE_DAMAGE = 'damage';

    const FILE_IMAGE = 'image';

    const TYPE_AVATAR = 'avatar';
    const TYPE_GALLERY = 'gallery';

    protected $request;
    protected $file;
    protected $dataImage;
    protected $storagePath;
    protected $imageUrl;
    protected $thumbUrl;
    /** @var User */
    protected $user;

    /**
     * UploadFileService constructor.
     *
     * @param $request
     * @throws CustomValidationException
     */
    public function __construct($request)
    {
        if (!$request->file('file')) {
            throw new CustomValidationException('file is required');
        }
        $this->request = $request;
        $this->file = $request->file('file');
        $this->user = $request->user();
        if(!$this->user) {
            throw new ResourceNotFoundException(User::class);
        }
        $this->dataImage = [];
        $this->uploadFileToS3();
    }

    abstract public function upload();

    private function uploadFileToS3()
    {
        $this->prepareFile();
    }

    /**
     * @param $path
     * @param $stream
     * @param string $access
     * @return string|null
     */
    private function putToS3($path, $stream, $access = self::ACCESS_PUBLIC)
    {
        $url = config('filesystems.disks.s3.url');
        if (Storage::disk('s3')->put($path, $stream, $access)) {
            return $url . $path;
        }
        return null;
    }

    private function prepareFile()
    {
        $this->dataImage['file_key']        = (string)Str::uuid();
        $this->dataImage['file_extension']  = $this->file->getClientOriginalExtension();
        $this->dataImage['file_mime']       = $this->file->getClientMimeType();
        $this->dataImage['file_size']       = $this->file->getSize();
        // storage path
        $this->dataImage['exifData']        = $this->imageMetadata(Image::make($this->file)->exif());

        $this->dataImage['file_name'] = $this->dataImage['file_key'] . '.' . $this->dataImage['file_extension'];
        $path = $this->storagePath . $this->dataImage['file_name'];
        $this->imageUrl = $this->putToS3($path, file_get_contents($this->file));
    }

    /**
     * @param $data
     * @return mixed
     */
    private function imageMetadata($data)
    {
        if(isset($data['GPSLatitude'])) {
            $lat = eval('return ' . $data['GPSLatitude'][0] . ';')
                + (eval('return ' . $data['GPSLatitude'][1] . ';') / 60)
                + (eval('return ' . $data['GPSLatitude'][2] . ';') / 3600);
            $lng = eval('return ' . $data['GPSLongitude'][0] . ';')
                + (eval('return ' . $data['GPSLongitude'][1] . ';') / 60)
                + (eval('return ' . $data['GPSLongitude'][2] . ';') / 3600);
            $data['location'] = [
                'latitude'  => $lat,
                'longitude' => $lng
            ];
        }
        return $data;
    }
}
