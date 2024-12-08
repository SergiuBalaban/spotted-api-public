<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
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
            'lat'     => $this->lat,
            'long'    => $this->long,
        ];
    }
}
