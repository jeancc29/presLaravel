<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    protected $fillable = [
        "id", "idUsuario", "idCaja", "idEmpresa", "idCliente", "idPrestamo", "idTipoPago", "monto", "descuento", "devuelta", "comentario", "concepto", "status", "fecha", "esAbonoACapital", "idTipoAbonoACapital", "esRenegociacion", "idRenegociacion"
    ];

    public function user()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\User', 'id', 'idUsuario');
    }

    public function caja()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Box', 'id', 'idCaja');
    }

    public function company()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Company', 'id', 'idEmpresa');
    }

    public function customer()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Customer', 'id', 'idCliente');
    }

    public function type()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipoPago');
    }

    public function loan()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Loan', 'id', 'idPrestamo');
    }

    public function detail()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Paydetail', 'idPago');
    }
    public function abonodetail()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Abonodetail', 'idPago');
    }

    public function capitalPaymentType()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Type', 'idTipoAbonoACapital');
    }

    public function renegotiation()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Renegotiation', 'id', 'idRenegociacion');
    }

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
        WHERE p.idEmpresa = $idEmpresa AND p.status = 1 $queryPrestamo
        ORDER BY p.id DESC
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

    public static function exists($idPrestamo){
        $data = \DB::select("
            SELECT COUNT(pays.id) AS pagosRealizados FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1
        ");

        return ($data[0]->pagosRealizados > 0) ? true : false;
    }
}
