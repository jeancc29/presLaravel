<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSmallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "photo" => (isset($this->photo)) ? base64_encode(file_get_contents(\App\Classes\Helper::path() . $this->photo, true)) : null,
            "names" => $this->names,
            "surnames" => $this->surnames,
            "document" => $this->document,
            "contact" => $this->contact,
            "job" => $this->job,
        ];
    }
}
