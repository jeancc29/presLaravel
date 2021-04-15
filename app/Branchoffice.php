<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branchoffice extends Model
{
    protected $fillable = [
        'id', 
        'nombre', 
        'foto', 
        'direccion', 
        'ciudad', 
        'telefono1', 
        'telefono2', 
        'gerenteSucursal', 
        'gerenteCobro', 
        'status', 
        'idEmpresa', 
    ];

    public static function removeUsers($idSucursal){
        \DB::select("
            UPDATE users 
            SET 
                users.idSucursal = null
            WHERE users.idSucursal = $idSucursal
        ");
    }
}
