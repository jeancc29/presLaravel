<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "nombres" => $this->nombres,
            "apellidos" => $this->apellidos,
            "usuario" => $this->usuario,
            "contacto" => $this->contacto,
            "rol" => $this->rol,
            "empresa" => $this->empresa,
            "sucursal" => $this->sucursal,
            "permisos" => $this->permisos,
            "status" => $this->status,
        ];
    }
}
