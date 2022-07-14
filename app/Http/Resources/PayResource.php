<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayResource extends JsonResource
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
        $sumaCapitalInteresMoraDelDetallePago = $this->esAbonoACapital ? null : $this->detail()->selectRaw("sum(capital) capital, sum(interes) interes, sum(mora) mora")->get();

        return [
             "id" => $this->id,
             "concepto" => $this->concepto,
             "cliente" => new CustomerSmallResource($this->customer),
             "usuario" => $this->user,
             "monto" => $this->monto,
             "fecha" => $this->fecha,
             "esAbonoACapital" => $this->esAbonoACapital,
             "esRenegociacion" => $this->esRenegociacion,
             "capital" => $this->esAbonoACapital ? $this->monto : $sumaCapitalInteresMoraDelDetallePago[0]->capital,
             "interes" => $this->esAbonoACapital ? 0 : $sumaCapitalInteresMoraDelDetallePago[0]->interes,
             "mora" => $this->esAbonoACapital ? 0 : $sumaCapitalInteresMoraDelDetallePago[0]->mora,
             "descuento" => $this->descuento,
             "capitalPendiente" => $this->capitalPendiente,
             "tipoPago" => $this->type,
             "caja" => $this->box
         ];
    }
}
