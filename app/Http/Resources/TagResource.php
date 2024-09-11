<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tagName' => $this->tagName,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,  // Handle null case
        ];
    }    
}
