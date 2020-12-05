<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        "id", "phone", "extension", 
        "mobile", "fax", "email", "facebook",
        "instagram"
    ];
}
