<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = [
        'descripcion', 
        'balanceInicial', 
    ];
}
