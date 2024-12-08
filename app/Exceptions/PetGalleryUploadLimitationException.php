<?php

namespace App\Exceptions;

class PetGalleryUploadLimitationException extends ForbiddenException
{
    /**
     * UserSMSCodeExpiredException constructor.
     */
    public function __construct()
    {
        $message = 'You have exceded the maximum uploads of pet images.';
        $code = 'error_max_pet_gallery_images_reached';
        parent::__construct(json_encode(['message' => $message, 'code' => $code]));
    }
}
