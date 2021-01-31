<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            "id" => $this->id,
            "foto" => $this->foto,
            "nombre" => $this->nombre,
            "diasGracia" => $this->diasGracia,
            "porcentajeMora" => $this->porcentajeMora,
            "direccion" => new \App\Http\Resources\AddressResource($this->direccion),
            "moneda" => $this->moneda,
            "contacto" => $this->contacto,
            "tipoMora" => $this->tipoMora,
        ];
    }
}
