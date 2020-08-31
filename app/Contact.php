<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        "id", "telefono", "extension", 
        "celular", "fax", "correo", "facebook",
        "instagram"
    ];
}
