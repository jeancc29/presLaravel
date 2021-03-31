<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response; 

class Box extends Model
{
    protected $fillable = [
        'descripcion', 
        'balanceInicial', 
        'validarDesgloseEfectivo', 
        'validarDesgloseCheques', 
        'validarDesgloseTarjetas', 
        'validarDesgloseTransferencias', 
        'idEmpresa', 
    ];

    public static function validateMonto($caja, $monto = 0){
        if($caja == null)
            return true;
        
        $caja = Box::whereId($caja["id"])->first();
        if($caja == null){
            abort(402, "La caja no existe");
            return;
        }

        return 
        $caja->balance < abs($monto)
        ?
        abort(404, "La caja no tiene monto suficiente.")
        :
        true;
    }

    public static function updateBalance($idCaja){
        // $idCaja = (is_object($caja)) ? $caja->id : $caja["id"];

        \DB::select("
            UPDATE boxes SET balance = (
                SELECT
                    IF(balance.balance IS NOT NULL, balance.balance, 0)
                FROM (
                    SELECT SUM(transactions.monto) as balance FROM transactions WHERE transactions.idCaja = $idCaja AND transactions.status = 1
                ) AS balance
            ) 
            WHERE id = $idCaja
        ");
    }

    public static function customAll($ids){
        $idCajas = implode(", ", $ids);
        $cajas = \DB::select("
            SELECT
            b.id,
            b.descripcion,
            b.balance,
            b.balanceInicial,
            (SELECT 
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', t.id,
                        'monto', t.monto,
                        'comentario', t.comentario,
                        'fecha', t.created_at,
                        'usuario', (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)),
                        'tipo', (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion))
                    )
                ) 
                FROM transactions AS t 
                INNER JOIN users u ON u.id = t.idUsuario
                INNER JOIN types tp ON tp.id = t.idTipo
                WHERE t.idCaja = b.id AND t.status = 1
            ) transacciones,
            (
                SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', c.id,
                        'monto', c.monto,
                        'montoCheques', c.montoCheques,
                        'montoTarjetas', c.montoTarjetas,
                        'montoTransferencias', c.montoTransferencias,
                        'comentario', c.comentario,
                        'fecha', c.created_at,
                        'usuario', (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario))
                    )
                )
                FROM (
                    SELECT 
                    *
                    FROM
                    closures
                    WHERE closures.idCaja = b.id 
                    ORDER BY closures.created_at desc
                ) c
                INNER JOIN users u ON u.id = C.idUsuario
                WHERE c.idCaja = b.id
                
            ) cierres
            FROM boxes b
            WHERE  b.id in ($idCajas)
        ");

        return  $cajas;
    }

    public static function customFirst($idCaja){
        $cajas = \DB::select("
            SELECT
            b.id,
            b.descripcion,
            b.balance,
            b.balanceInicial,
            (SELECT 
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', t.id,
                        'monto', t.monto,
                        'comentario', t.comentario,
                        'fecha', t.created_at,
                        'usuario', (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)),
                        'tipo', (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion))
                    )
                ) 
                FROM (SELECT * FROM transactions WHERE transactions.idCaja = b.id ORDER BY  transactions.id DESC) AS t 
                INNER JOIN users u ON u.id = t.idUsuario
                INNER JOIN types tp ON tp.id = t.idTipo
                WHERE t.idCaja = b.id AND t.status = 1
                ORDER BY t.id DESC
            ) transacciones,
            (
                SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', c.id,
                        'monto', c.monto,
                        'montoCheques', c.montoCheques,
                        'montoTarjetas', c.montoTarjetas,
                        'montoTransferencias', c.montoTransferencias,
                        'comentario', c.comentario,
                        'fecha', c.created_at,
                        'usuario', (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario))
                    )
                )
                FROM (
                    SELECT 
                    *
                    FROM
                    closures
                    WHERE closures.idCaja = b.id 
                    ORDER BY closures.created_at desc
                ) c
                INNER JOIN users u ON u.id = C.idUsuario
                WHERE c.idCaja = b.id
            ) cierres
            FROM boxes b
            WHERE  b.id = $idCaja
        ");

        return  count($cajas) > 0 ? $cajas[0] : null;
    }

    public static function transacciones($idCaja){
        // return \DB::select("
        //     SELECT 
        //     JSON_ARRAYAGG(
        //         JSON_OBJECT(
        //             'id', t.id,
        //             'monto', t.monto,
        //             'comentario', t.comentario,
        //             'fecha', t.created_at,
        //             'usuario', (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)),
        //             'tipo', (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion))
        //         )
        //     ) 
        //     FROM transactions AS t 
        //     INNER JOIN users u ON u.id = t.idUsuario
        //     INNER JOIN types tp ON tp.id = t.idTipo
        //     WHERE t.idCaja = $idCaja AND t.status = 1
        // ");
        return \DB::select("
            SELECT 
            
                    t.id,
                    t.monto,
                    t.comentario,
                    t.created_at,
                    (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)) usuario,
                    (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion)) tipo
            FROM (SELECT * FROM transactions WHERE transactions.idCaja = $idCaja AND transactions.status = 1) AS t 
            INNER JOIN users u ON u.id = t.idUsuario
            INNER JOIN types tp ON tp.id = t.idTipo
            WHERE t.idCaja = $idCaja AND t.status = 1
            ORDER BY t.id DESC
        ");
    }

    public static function cierres($idCaja){
        
        return \DB::select("
            SELECT 
            
                    c.id,
                    c.monto,
                    c.montoCheques,
                    c.montoTarjetas,
                    c.montoTransferencias,
                    c.comentario,
                    c.created_at,
                    (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos, 'usuario', u.usuario)) usuario
            FROM closures AS c
            INNER JOIN users u ON u.id = c.idUsuario
            WHERE c.idCaja = $idCaja AND c.status = 1
        ");
    }


    public function transactions()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Transaction', 'idCaja');
    }


}
