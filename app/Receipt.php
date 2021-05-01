<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        "idEmpresa",
        "copia",
        "capital",
        "mora",
        "interes",
        "descuento",
        "capitalPendiente",
        "balancePendiente",
        "fechaProximoPago",
        "formaPago",
        "firma",
    ];
}
