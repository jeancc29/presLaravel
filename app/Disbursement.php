<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    protected $fillable = [
        "id", 
        "idType", 
        "idBank", 
        "idAccount", 
        "checkNumber", 
        "idBankDestination",
        "idAccountDestination",
        "grossAmount",
        "netAmount",
    ];
}
