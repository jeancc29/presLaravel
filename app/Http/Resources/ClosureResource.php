<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClosureResource extends JsonResource
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
            "caja" => $this->box,
            "usuario" => $this->user,
            "totalSegunUsuario" => $this->totalSegunUsuario,
            "totalSegunSistema" => $this->totalSegunSistema,
            "diferencia" => $this->diferencia,
            "comentario" => $this->comentario,
            "created_at" => $this->created_at,
        ];
    }
}
