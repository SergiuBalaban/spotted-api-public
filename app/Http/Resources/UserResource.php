<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param null $request
     * @return array
     */
    public function toArray($request = null)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar'     => $this->avatar()->pluck('root')->first(),
            'birthday'   => $this->birthday,
            'status'     => $this->status,
            'sex'        => $this->sex,
        ];
    }
}
