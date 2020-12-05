<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;   
    protected $fillable = [
        'date', 
        'concept', 
        'amount', 
        'commentary', 
        'idBox', 
        'idType', 
        'idUser', 
    ];

    public function type()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idType');
    }

    public function box()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Box', 'id', 'idBox');
    }

}
