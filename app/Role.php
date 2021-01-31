<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        "id", "descripcion", "idEntidad", "idEmpresa"
    ];

    public function permisos()
    {
        return $this->belongsToMany('App\Permission', 'permission_role', 'idRol', 'idPermiso')->withPivot('created_at');
    }
}
