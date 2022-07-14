<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Renegotiation extends Model
{
    //

    protected $fillable = [
        "idUsuario",
        "idPrestamo",
        "monto",
        "porcentajeInteres",
        "porcentajeInteresAnual",
        "montoInteres",
        "numeroCuotas",
        "fecha",
        "fechaPrimerPago",
        "porcentajeMora",
        "diasGracia",
        "capitalTotal",
        "interesTotal",
        "capitalPendiente",
        "interesPendiente",
        "mora",
        "cuota",
        "numeroCuotasPagadas",
        "cuotasAtrasadas",
        "diasAtrasados",
        "fechaProximoPago",
        "idTipoPlazo",
        "idTipoAmortizacion",
    ];

    public function detail()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Renegotiationdetail', 'idRenegociacion');
    }

}
