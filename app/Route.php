<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'descripcion', 'idEmpresa'
    ];

    public static function customAll($idEmpresa){
        return \DB::select("
            SELECT * FROM routes WHERE routes.idEmpresa = $idEmpresa
        ");
    }
}
