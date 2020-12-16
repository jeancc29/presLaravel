<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    protected $fillable = [
        "id", 
        "idTipo", 
        "idBanco", 
        "idCuenta", 
        "numeroCheque", 
        "idBancoDestino",
        "cuentaDestino",
        "montoBruto",
        "montoNeto",
    ];
}
