<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        "id", "descripcion"
    ];

    public function permisos()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Permission', 'idEntidad');
    }
}
