<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    protected $fillable = [
        "id", 
        "numeroCuota", 
        "idTipo", 
        "idPrestamo", 
        "cuota", 
        "interes", 
        "capital",
        "capitalRestante",
        "capitalSaldado",
        "interesSaldado",
        "fecha",
    ];
}
