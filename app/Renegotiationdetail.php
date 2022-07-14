<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Renegotiationdetail extends Model
{
    //
    protected $fillable = [
        "idRenegociacion",
        "idPrestamo",
        "idTipo",
        "numeroCuota",
        "cuota",
        "interes",
        "capital",
        "mora",
        "capitalRestante",
        "capitalSaldado",
        "interesSaldado",
        "capitalPendiente",
        "interesPendiente",
        "moraPendiente",
        "pagada",
        "fecha",
    ];
}
