<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    protected $fillable = [
        "id", 
        "idType", 
        "idLoan", 
        "quota", 
        "interest", 
        "capital",
        "remainingCapital",
        "paidCapital",
        "paidInterest",
        "date",
    ];
}
