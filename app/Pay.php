<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    protected $fillable = [
        "id", "idUsuario", "idCaja", "idEmpresa", "idTipoPago", "monto", "devuelta", "comentario"
    ];
}
