<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paydetail extends Model
{
    protected $fillable = [
        "id", "idPago", "idAmortizacion", "capital", "interes", "mora", "devuelta", "comentario"
    ];
}
