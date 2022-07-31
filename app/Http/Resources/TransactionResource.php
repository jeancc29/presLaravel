<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "idEmpresa" => $this->idEmpresa,
            "usuario" => $this->user,
            "caja" => $this->box,
            "monto" => $this->incomeOrExpenseType->descripcion == "Egresos" ? -1 * $this->monto : $this->monto,
            "comentario" => $this->comentario,
            "status" => $this->status,
            "tipo" => $this->type,
            "created_at" => $this->created_at
        ];
    }
}
