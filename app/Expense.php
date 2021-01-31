<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;   
    protected $fillable = [
        'fecha', 
        'concepto', 
        'monto', 
        'comentario', 
        'idCaja', 
        'idTipo', 
        'idUsuario', 
        'idEmpresa', 
    ];

    public function tipo()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipo');
    }

    public function caja()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Box', 'id', 'idCaja');
    }

}
