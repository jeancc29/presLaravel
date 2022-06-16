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
//            "id" => $this->id,
//            "nombres" => $this->nombres,
//            "apellidos" => $this->apellidos,
//            "documento" => $this->documento,
//            "contacto" => $this->contacto,
//            "idRuta" => $this->idRuta,
            "id" => $this->id,
            "nombres" => $this->nombres,
            "apellidos" => $this->apellidos,
            "nombreFoto" => $this->foto,
        ];
        // return parent::toArray($request);
    }
}
