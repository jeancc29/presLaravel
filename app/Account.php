<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        "id", "descripcion", "idBanco"
    ];

    public function banco()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Bank', 'id', 'idBanco');
    }
}
