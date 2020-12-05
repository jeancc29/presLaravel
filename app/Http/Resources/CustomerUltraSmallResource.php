<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerUltraSmallResource extends JsonResource
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
            "names" => $this->names,
            "surnames" => $this->surnames,
            "document" => $this->document,
            "contact" => $this->contact,
        ];
        // return parent::toArray($request);
    }
}
