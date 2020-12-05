<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    protected $fillable = [
        "id", 
        "idLoan", 
        "idType", 
        "description", 
        "appraisal", 
        "vehicleRegistrationNumber", 
        "brand", 
        "chassis", 
        "status", 
        "vehiclePlate", 
        "yearOfProduction", 
        "motorOrSeries", 
        // "idTipoEmision", 
        "cylinders", 
        "color", 
        "numberOfPassengers", 
        "idTypeCondition", 
        "numberOfDoors", 
        "motorForce", 
        "loadCarryingCapacity", 
        "previousVehiclePlate", 
        "expeditionDate", 
        "photo",
        "vehicleRegistrationNumberPhoto",
        "licensePhoto",
    ];
}
