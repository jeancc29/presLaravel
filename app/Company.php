<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        "id", "nombre", "foto", "diasGracia", "porcentajeMora",
        "idDireccion", "idContacto", "idTipoMora", "idMoneda", "status"
    ];

    public function direccion()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Address', 'id', 'idDireccion');
    }

    public function contacto()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Contact', 'id', 'idContacto');
    }

    public function moneda()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Coin', 'id', 'idMoneda');
    }

    public function tipoMora()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipoMora');
    }
}
