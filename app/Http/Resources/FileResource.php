<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'id'       => @$this->id,
            'root'     => @$this->root,
        ];
    }
}
