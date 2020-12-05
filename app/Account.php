<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        "id", "description", "idBank"
    ];

    public function banco()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Bank', 'id', 'idBank');
    }
}
