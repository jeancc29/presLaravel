<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loansetting extends Model
{
    protected $fillable = [
        "id", 
        "garantia", 
        "gasto", 
    ];
}
