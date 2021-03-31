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
            "id" => $this->id,
            "foto" => (isset($this->foto)) ? base64_encode(file_get_contents(\App\Classes\Helper::path() . $this->foto, true)) : null,
            "nombres" => $this->nombres,
            "apellidos" => $this->apellidos,
            "documento" => $this->documento,
            "contacto" => $this->contacto,
            "trabajo" => $this->trabajo,
            "idEmpresa" => $this->idEmpresa,
        ];
    }
}
