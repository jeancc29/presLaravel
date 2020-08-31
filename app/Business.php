<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        "id", "nombre", "tipo", 
        "tiempoExistencia", "idDireccion",
        "idContacto", "idCliente"
    ];
}
