<?php

namespace App\Http\Resources;

use App\Amortization;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
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
//            "id" => $this->id,
//            "idEmpresa" => $this->idEmpresa,
//            "cliente" => new CustomerSmallResource($this->customer),
//            "idEmpresa" => $this->idEmpresa,
//            "monto" => $this->monto,
//            "porcentajeInteres" => $this->porcentajeInteres,
//            "porcentajeMora" => $this->porcentajeMora,
//            "cuota" => $this->cuota,
//            "numeroCuotas" => $this->numeroCuotas,
//            "balancePendiente" => $this->monto,
//            "capitalPendiente" => $this->capitalPendiente,
//            "interesPendiente" => $this->interesPendiente,
//            "numeroCuotasPagadas" => $this->numeroCuotasPagadas,
//            "fechaProximoPago" => $this->fechaProximoPago,
//            "tipo" => $this->type,
//            "tipoAmortizacion" => $this->amortizationType,
//            "tipoPlazo" => $this->termType,
//            "codigo" => $this->codigo,
//            "status" => $this->status,
//            "caja" => $this->box,
//            "amortizaciones" => $this->amortizations,
//            "pagos" => PayResource::collection($this->pays()->orderBy("id", "desc")->get())
            "id" => $this->id,
            "cliente" => new CustomerUltraSmallResource($this->customer),
            "monto" => $this->monto,
            "montoPrestado" => $this->monto,
            "porcentajeInteres" => $this->porcentajeInteres,
            "cuota" => $this->cuota,
            "numeroCuotas" => $this->monto,
            "numeroCuotasPagadas" => $this->numeroCuotasPagadas,
            "balancePendiente" => $this->balancePendiente,
            "capitalPendiente" => $this->capitalPendiente,
            "interesPendiente" => $this->interesPendiente,
            "fechaProximoPago" => $this->fechaProximoPago,
            "tipoAmortizacion" => new TypeResource($this->amortizationType),
            "codigo" => $this->codigo,
            "status" => $this->status,
            "caja" => new BoxResource($this->box),
            "cuotasAtrasadas" => $this->cuotasAtrasadas
        ];
    }
}
