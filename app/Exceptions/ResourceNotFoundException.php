<?php

namespace App\Exceptions;

use App\Models\Pet;
use App\Models\ReportedPet;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class ResourceNotFoundException extends ModelNotFoundException
{
    /**
     * DriverNotFoundException constructor.
     *
     * @param $model
     * @param Throwable|null $previous
     */
    public function __construct($model, Throwable $previous = null)
    {
        $message = $this->getMessageByClass($model);
        parent::__construct($message, 404, $previous);
    }

    /**
     * @param $model
     * @return string
     */
    private function getMessageByClass($model)
    {
        switch ($model) {
            case User::class:
                return 'User not found';
            case Pet::class:
                return 'Pet not found';
            case ReportedPet::class:
                return 'PetReport not found';
            default:
                return 'Resource not found';
        }
    }
}
