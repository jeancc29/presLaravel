<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanexpense extends Model
{
    protected $fillable = [
        "id", 
        "idPrestamo", 
        "idTipo", 
        "porcentaje", 
        "importe", 
        "incluirEnElFinanciamiento", 
    ];
}
