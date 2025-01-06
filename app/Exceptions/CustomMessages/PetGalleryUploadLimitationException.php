<?php

namespace App\Exceptions\CustomMessages;

class PetGalleryUploadLimitationException extends AbstractForbiddenException
{
    /**
     * @var string
     */
    protected $message = ErrorMessageValue::ERROR_MAX_IMAGES_PER_GALLERY_MESSAGE;

    /**
     * @var string
     */
    protected $code = ErrorMessageValue::ERROR_MAX_IMAGES_PER_GALLERY_CODE;
}
