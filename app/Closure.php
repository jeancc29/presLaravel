<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Closure extends Model
{
    protected $fillable = [
        'idUsuario', 
        'idEmpresa', 
        'idCaja', 
        'monto', 
        'montoCheques', 
        'montoTarjetas', 
        'montoTransferencias', 
        'comentario', 
        'status', 
    ];

    public function transactions()
    {
        return $this->belongsToMany('App\Transaction', 'closure_transaction', 'idCierre', 'idTransaccion');
    }

    public static function transacciones($idCierre){
       
        return \DB::select("
            SELECT 
                    t.id,
                    t.monto,
                    t.comentario,
                    t.created_at,
                    (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)) usuario,
                    (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion)) tipo
            FROM (
                SELECT 
                *
                FROM
                transactions
                WHERE
                transactions.id in (SELECT closure_transaction.idTransaccion FROM closure_transaction WHERE closure_transaction.idCierre = $idCierre)
            ) AS t 
            INNER JOIN users u ON u.id = t.idUsuario
            INNER JOIN types tp ON tp.id = t.idTipo
        ");
    }
}
