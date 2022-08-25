<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            "id" => $this->id,
            "foto" => $this->foto,
            "nombres" => $this->nombres,
            "apellidos" => $this->apellidos,
            "apodo" => $this->apodo,
            "numeroDependientes" => $this->numeroDependientes,
            "fechaNacimiento" => $this->fechaNacimiento,
            "idTipoSexo" => $this->idTipoSexo,
            "idTipoEstadoCivil" => $this->idTipoEstadoCivil,
            "estado" => $this->estado,
            "nacionalidad" => $this->nationality,
        ];
    }
}
