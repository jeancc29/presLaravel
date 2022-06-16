<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanWithAmortizationResource extends JsonResource
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
        // use prestamo;
        // select
        // a.id,
        // IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capital,
        // IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interes,
        // (SELECT mora(l.id, a.id)) as mora,
        // a.fecha,
        // DATEDIFF(CURDATE(), a.fecha) diasAtrasados
        // from amortizations a
        // inner join loans l on l.id = a.idPrestamo
        // left join paydetails pd on pd.idAmortizacion = a.id
        // group by a.id;

        return [
//            "id" => $this->id,
//            "concepto" => $this->concepto,
//            "monto" => $this->monto,
//            "fecha" => $this->fecha,
//            "comentario" => $this->comentario,
//            "idCaja" => $this->idCaja,
//            "caja" => $this->caja,
//            "idTipo" => $this->idTipo,
//            "tipo" => $this->tipo,
            "id" => $this->id,
            "idEmpresa" => $this->idEmpresa,
            "cliente" => new CustomerSmallResource($this->customer),
            "idEmpresa" => $this->idEmpresa,
            "monto" => $this->monto,
            "porcentajeInteres" => $this->porcentajeInteres,
            "porcentajeMora" => $this->porcentajeMora,
            "cuota" => $this->cuota,
            "numeroCuotas" => $this->numeroCuotas,
            "balancePendiente" => $this->monto,
            "capitalPendiente" => $this->capitalPendiente,
            "interesPendiente" => $this->interesPendiente,
            "numeroCuotasPagadas" => $this->numeroCuotasPagadas,
            "fechaProximoPago" => $this->fechaProximoPago,
            "tipo" => $this->type,
            "tipoAmortizacion" => $this->amortizationType,
            "tipoPlazo" => $this->termType,
            "codigo" => $this->codigo,
            "status" => $this->status,
            "caja" => $this->box,
            "amortizaciones" => $this->amortizations,
            "pagos" => PayResource::collection($this->pays()->orderBy("id", "desc")->get())
        ];
    }
}
