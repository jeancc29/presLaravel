<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $fillable = [
        "id", 
        "nombres", 
        "numeroIdentificacion", 
        "telefono", 
        "direccion", 
    ];
}
