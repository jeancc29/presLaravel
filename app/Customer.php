<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        "id", "photo", "names", "surnames", "nickname",
        "birthDate", "numberDependents",
        "gender", "maritalStatus", "status", "idContact",
        "idAddress", "idDocument", "residenceType", "timeInResidence", "referredBy",
        "idJob", "idBusiness", "nationality"
    ];

    public function document()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Document', 'id', 'idDocument');
    }

    public function contact()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Contact', 'id', 'idContact');
    }

    public function job()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Job', 'id', 'idJob');
    }
}
