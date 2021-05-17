<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Othersetting extends Model
{
    protected $fillable = [
        "idEmpresa",
        "ocultarInteresAmortizacion",
        "requirirSeleccionarCaja",
        "calcularComisionACuota",
    ];
}
