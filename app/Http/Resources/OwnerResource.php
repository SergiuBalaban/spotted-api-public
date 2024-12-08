<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
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
            'avatar'     => $this->avatar()->pluck('root')->first(),
        ];
    }
}
