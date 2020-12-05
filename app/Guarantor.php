<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $fillable = [
        "id", 
        "names", 
        "identification", 
        "phone", 
        "address", 
    ];
}
