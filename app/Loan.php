<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        "id", 
        "idUsuario", 
        "idCliente", 
        "idTipoPlazo", 
        "idTipoAmortizacion", 
        "idCaja", 
        "idCobrador", 
        // "idGasto", 
        // "idGarante", 
        "idDesembolso",
        "monto", 
        "porcentajeInteres", 
        "porcentajeInteresAnual", 
        "numeroCuotas", 
        "fecha", 
        "fechaPrimerPago", 
        "codigo", 
        "porcentajeMora", 
        "diasGracia", 
    ];
}
