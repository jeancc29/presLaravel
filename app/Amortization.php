<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    protected $fillable = [
        "id", 
        "idPrestamo", 
        "cuota", 
        "interes", 
        "capital",
        "capitalRestante",
        "capitalSaldado",
    ];
}
