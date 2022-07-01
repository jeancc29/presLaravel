<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Abonodetail extends Model
{
    protected $fillable = [
        "id",
        "numeroCuota",
        "idTipo",
        "idPrestamo",
        "idPago",
        "cuota",
        "interes",
        "capital",
        "capitalRestante",
        "capitalSaldado",
        "interesSaldado",
        "fecha",
        "capitalPendiente",
        "interesPendiente",
        "moraPendiente",
        "pagada",
    ];
}
