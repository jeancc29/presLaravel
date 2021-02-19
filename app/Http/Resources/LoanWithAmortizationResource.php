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
