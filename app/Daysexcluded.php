<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Daysexcluded extends Model
{
    protected $fillable = [
        "id", 
        "idLoan", 
        "idDay", 
    ];
}
