<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            "direccion" => $this->direccion,
            "ciudad" => $this->ciudad,
            "estado" => $this->estado,
            "sector" => $this->sector,
            "numero" => $this->numero,
        ];
    }
}
