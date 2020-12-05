<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        "id", "address", "sector", "number",
        "idState", "idCity"
    ];
}
