<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loanexpense extends Model
{
    protected $fillable = [
        "id", 
        "idTipo", 
        "porcentaje", 
        "importe", 
    ];
}
