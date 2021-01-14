<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        "id", "descripcion", "idEntidad"
    ];

    public function entidad()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Entity', 'id', 'idEntidad');
    }
}
