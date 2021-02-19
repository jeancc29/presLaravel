<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    protected $fillable = [
        "id", "idUsuario", "idCaja", "idEmpresa", "idCliente", "idPrestamo", "idTipoPago", "monto", "descuento", "devuelta", "comentario", "concepto", "status", "fecha"
    ];

    public static function customAll($idEmpresa, $idPrestamo = null, $arrayOfLimit = array(0, 50))
    {
        // return (new static)::where('week', $week)->first();
        $limit = implode(", ", $arrayOfLimit);
        $queryPrestamo = ($idPrestamo != null) ? " AND p.idPrestamo = $idPrestamo" : "";
        // return "limit $limit";
        return \DB::select("
        SELECT
            p.id,
            p.concepto,
            (SELECT JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
            (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos)) as usuario,
            p.monto,
            p.fecha,
            (SELECT SUM(paydetails.capital) FROM paydetails WHERE paydetails.idPago = p.id) AS capital,
            (SELECT SUM(paydetails.interes) FROM paydetails WHERE paydetails.idPago = p.id) AS interes,
            (SELECT SUM(paydetails.mora) mora FROM paydetails WHERE paydetails.idPago = p.id) AS mora,
            p.descuento,
            l.capitalPendiente as capitalPendiente,
            (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = p.idTipoPago) as tipoPago,
            l.codigo codigo,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja
        FROM pays p
        INNER JOIN customers c ON c.id = p.idCliente 
        INNER JOIN users u ON u.id = p.idUsuario 
        INNER JOIN loans l ON l.id = p.idPrestamo 
        INNER JOIN types t ON t.id = p.idTipoPago 
        LEFT JOIN boxes b ON b.id = p.idCaja 
        WHERE p.idEmpresa = $idEmpresa $queryPrestamo
        LIMIT $limit ");
    }

    public static function customFirst($idPago)
    {
        // return (new static)::where('week', $week)->first();
        // $limit = implode(", ", $arrayOfLimit);
        // $queryPrestamo = ($idPrestamo != null) ? " AND p.idPrestamo = $idPrestamo" : "";
        // return "limit $limit";
        $pago =  \DB::select("
        SELECT
            p.id,
            p.concepto,
            (SELECT JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
            (SELECT JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'apellidos', u.apellidos)) as usuario,
            p.monto,
            p.fecha,
            (SELECT SUM(paydetails.capital) FROM paydetails WHERE paydetails.idPago = p.id) AS capital,
            (SELECT SUM(paydetails.interes) FROM paydetails WHERE paydetails.idPago = p.id) AS interes,
            (SELECT SUM(paydetails.mora) mora FROM paydetails WHERE paydetails.idPago = p.id) AS mora,
            p.descuento,
            l.capitalPendiente as capitalPendiente,
            (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = p.idTipoPago) as tipoPago,
            l.codigo codigo,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja
        FROM pays p
        INNER JOIN customers c ON c.id = p.idCliente 
        INNER JOIN users u ON u.id = p.idUsuario 
        INNER JOIN loans l ON l.id = p.idPrestamo 
        INNER JOIN types t ON t.id = p.idTipoPago 
        LEFT JOIN boxes b ON b.id = p.idCaja 
        WHERE p.id = $idPago
        LIMIT 1 ");

        return (count($pago) > 0) ? $pago[0] : null;
    }
}
