<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "id", "direccion", "sector", "numero",
        "idEstado", "idCiudad"
    ];
}
