<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        "id",
        "nombre",
        "tipo",
        "tiempoExistencia",
        "idDireccion",
        "idContacto",
        "idCliente"
    ];

    public function address()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Address', 'id', 'idDireccion');
    }
}
