<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        "id", 
        "idUser", 
        "idCustomer", 
        "idTypeTerm", 
        "idTypeAmortization", 
        "idBox", 
        "idCollector", 
        "idExpense", 
        "idGuarantor", 
        "idDisbursement",
        "amount", 
        "interestPercent", 
        "annualInterestPercent", 
        "quotas", 
        "date", 
        "firstPaymentDate", 
        "uniqueCode", 
        "penaltyPercent", 
        "daysOfGrace", 
    ];
}
