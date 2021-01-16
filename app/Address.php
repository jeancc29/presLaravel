<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "id", "direccion", "sector", "numero",
        "idEstado", "idCiudad", "idPais"
    ];

    public function ciudad()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\City', 'id', 'idCiudad');
    }

    public function estado()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\State', 'id', 'idEstado');
    }

}
