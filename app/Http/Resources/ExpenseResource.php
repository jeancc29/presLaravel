<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            "concepto" => $this->concepto,
            "monto" => $this->monto,
            "fecha" => $this->fecha,
            "comentario" => $this->comentario,
            "idCaja" => $this->idCaja,
            "caja" => $this->caja,
            "idTipo" => $this->idTipo,
            "tipo" => $this->tipo,
        ];
    }
}
