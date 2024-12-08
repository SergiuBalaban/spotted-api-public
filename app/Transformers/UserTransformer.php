<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $settings;

    public function __construct() {}

    public function transform(User $user)
    {
        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'status'   => $user->status,
            'sex'      => $user->sex,
            'active'   => $user->active,
        ];
    }
}
