<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        "id", "nombre", 
        "ocupacion", "ingresos", "otrosIngresos",
        "fechaIngreso", "idDireccion", "idCliente",
        "idContacto"
    ];
}
