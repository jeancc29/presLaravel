<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $fillable = [
        'description', 
        'initialBalance', 
        'validateCashBreakdown', 
        'validateCheckBreakdown', 
        'validateCrediCardBreakdown', 
        'validateTransferBreakdown', 
    ];
}
