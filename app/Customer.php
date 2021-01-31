<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        "id", "foto", "nombres", "apellidos", "apodo",
        "fechaNacimiento", "numeroDependientes",
        "sexo", "estadoCivil", "estado", "idContacto",
        "idDireccion", "idDocumento", "tipoVivienda", "tiempoEnVivienda", "referidoPor",
        "idTrabajo", "idNegocio", "idEmpresa"
    ];

    public function documento()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Document', 'id', 'idDocumento');
    }

    public function contacto()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Contact', 'id', 'idContacto');
    }

    public function trabajo()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Job', 'id', 'idTrabajo');
    }
}
