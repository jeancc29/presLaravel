<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "id", "direccion", "direccion2", "codigoPostal"
    ];

//    public function ciudad()
//    {
//        //Modelo, foreign key, local key
//        return $this->hasOne('App\City', 'id', 'idCiudad');
//    }
//
//    public function estado()
//    {
//        //Modelo, foreign key, local key
//        return $this->hasOne('App\State', 'id', 'idEstado');
//    }

}
