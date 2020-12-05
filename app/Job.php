<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        "id", "name", 
        "occupation", "income", "otherIncome",
        "admissionDate", "idAddress", "idCustomer",
        "idContact"
    ];
}
