<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    protected $fillable = [
        "id", 
        "idPrestamo", 
        "idTipo", 
        "descripcion", 
        "tasacion", 
        "matricula", 
        "marca", 
        "chasis", 
        "estado", 
        "placa", 
        "anoFabricacion", 
        "motorOSerie", 
        "idTipoEmision", 
        "cilindros", 
        "color", 
        "numeroPasajeros", 
        "idTipoCondicion", 
        "numeroPuertas", 
        "fuerzaMotriz", 
        "capacidadCarga", 
        "placaAnterior", 
        "fechaExpedicion", 
    ];
}
