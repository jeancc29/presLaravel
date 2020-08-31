<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $fillable = [
        "id", "nombre", "tipo", "parentesco",
        "idCliente"
    ];
}
