<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        "id", 
        "idUsuario", 
        "idCliente", 
        "idTipoPlazo", 
        "idTipoAmortizacion", 
        "idCaja", 
        "idEmpresa", 
        "idCobrador", 
        // "idGasto", 
        // "idGarante", 
        "idDesembolso",
        "monto", 
        "montoInteres", 
        "porcentajeInteres", 
        "porcentajeInteresAnual", 
        "numeroCuotas", 
        "fecha", 
        "fechaPrimerPago", 
        "codigo", 
        "porcentajeMora", 
        "diasGracia", 
        "capitalPendiente", 
        "interesPendiente", 
        "numeroCuotasPagadas", 
        "fechaProximoPago", 
        "status", 
    ];

    public static function customAll($idEmpresa, $arrayOfLimit = array(0, 50))
    {
        // return (new static)::where('week', $week)->first();
        $limit = implode(", ", $arrayOfLimit);
        // return "limit $limit";
        return \DB::select("
        SELECT
            l.id,
            (SELECT JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
            l.monto,
            l.porcentajeInteres,
            (select cuota FROM amortizations WHERE amortizations.idPrestamo = l.id LIMIT 1) as cuota,
            l.numeroCuotas,
            l.monto as balancePendiente,
            l.capitalPendiente as capitalPendiente,
            l.interesPendiente as interesPendiente,
            l.numeroCuotasPagadas as numeroCuotasPagadas,
            l.fechaProximoPago,
            (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = l.idTipoAmortizacion) as tipoAmortizacion,
            l.codigo codigo,
            l.status,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja,
            (
                SELECT
                COUNT(IF(l.diasGracia > a.diasAtrasados, null, a.id))
                FROM (
                    SELECT
                    a.id,
                    IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
                    a.numeroCuota,
                    a.capital,
                    a.interes,
                    a.cuota,
                    a.capitalRestante,
                    a.interesRestante,
                    a.capitalRestante + a.interesRestante as cuotaRestante,
                    a.fecha,
                    a.diasAtrasados,
                    a.mora
                    FROM   (
                            SELECT
                                a.id,
                                a.numeroCuota,
                                a.capital,
                                a.interes,
                                IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
                                IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
                                IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
                                a.cuota,
                                a.fecha,
                                DATEDIFF(CURDATE(), a.fecha) diasAtrasados
                            FROM (SELECT * from amortizations where amortizations.idPrestamo = l.id AND DATEDIFF(CURDATE(), amortizations.fecha) >= 0) as a
                            LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
                            WHERE a.idPrestamo = l.id
                            GROUP BY a.id
                        ) AS a
                    order by a.id
                ) AS a
                WHERE a.pagada = 0 AND a.diasAtrasados >= 0
            ) as cuotasAtrasadas
        FROM loans l 
        INNER JOIN customers c ON c.id = l.idCliente 
        INNER JOIN types t ON t.id = l.idTipoAmortizacion 
        LEFT JOIN boxes b ON b.id = l.idCaja 
        WHERE l.idEmpresa = $idEmpresa AND l.status = 1
        LIMIT $limit ");
    }

    public static function customFirst($idPrestamo)
    {
        // return (new static)::where('week', $week)->first();
        // return "limit $limit";
        // (
        //     SELECT
        //         SUM(amortizations.cuota) 
        //     FROM amortizations 
        //     WHERE 
        //         amortizations.idPrestamo = l.id 
        //     AND 
        //         DATEDIFF(CURDATE(), a.fecha) >= 0
        // ) AS balancePendiente,
        $prestamo = \DB::select("
        SELECT
            l.id,
            (SELECT JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
            l.monto,
            l.porcentajeInteres,
            (select cuota FROM amortizations WHERE amortizations.idPrestamo = l.id LIMIT 1) as cuota,
            l.numeroCuotas,
            0 AS balancePendiente,
            l.capitalPendiente as capitalPendiente,
            l.interesPendiente as interesPendiente,
            l.numeroCuotasPagadas as numeroCuotasPagadas,
            l.fechaProximoPago,
            (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = l.idTipoAmortizacion) as tipoAmortizacion,
            l.codigo codigo,
            l.status,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja,
            (
                SELECT
                COUNT(IF(l.diasGracia > a.diasAtrasados, null, a.id))
                FROM (
                    SELECT
                    a.id,
                    IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
                    a.numeroCuota,
                    a.capital,
                    a.interes,
                    a.cuota,
                    a.capitalRestante,
                    a.interesRestante,
                    a.capitalRestante + a.interesRestante as cuotaRestante,
                    a.fecha,
                    a.diasAtrasados,
                    a.mora
                    FROM   (
                            SELECT
                                a.id,
                                a.numeroCuota,
                                a.capital,
                                a.interes,
                                IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
                                IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
                                IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
                                a.cuota,
                                a.fecha,
                                DATEDIFF(CURDATE(), a.fecha) diasAtrasados
                            FROM (SELECT * from amortizations where amortizations.idPrestamo = l.id AND DATEDIFF(CURDATE(), amortizations.fecha) >= 0) as a
                            LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
                            WHERE a.idPrestamo = l.id
                            GROUP BY a.id
                        ) AS a
                    order by a.id
                ) AS a
                WHERE a.pagada = 0 AND a.diasAtrasados >= 0
            ) as cuotasAtrasadas
        FROM loans l 
        INNER JOIN customers c ON c.id = l.idCliente 
        INNER JOIN types t ON t.id = l.idTipoAmortizacion 
        LEFT JOIN boxes b ON b.id = l.idCaja 
        WHERE l.id = $idPrestamo");

        return (count($prestamo) == 0) ? null : $prestamo[0];
    }

    public static function customFirstAmortizaciones($idPrestamo)
    {
        // return (new static)::where('week', $week)->first();
        // return "limit $limit";
        $prestamo = \DB::select("
        SELECT
            l.id,
            l.idEmpresa,
            (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto, 'documento', (SELECT JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)), 'contacto', (SELECT JSON_OBJECT('id', co.id, 'celular', co.celular, 'correo', co.correo)))) as cliente,
            l.monto,
            l.porcentajeInteres,
            l.porcentajeMora,
            (select cuota FROM amortizations WHERE amortizations.idPrestamo = l.id LIMIT 1) as cuota,
            l.numeroCuotas,
            l.monto as balancePendiente,
            l.capitalPendiente as capitalPendiente,
            l.interesPendiente as interesPendiente,
            l.numeroCuotasPagadas as numeroCuotasPagadas,
            l.fechaProximoPago,
            (SELECT JSON_OBJECT('id', t.id, 'descripcion', t.descripcion)) as tipoAmortizacion,
            (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion)) as tipoPlazo,
            l.codigo codigo,
            l.status,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja,
            (
                SELECT 
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', a.id,
                            'pagada', a.pagada,
                            'numeroCuota', a.numeroCuota,
                            'capital', a.capital,
                            'interes', a.interes,
                            'capitalRestante', a.capitalRestante,
                            'interesRestante', a.interesRestante,
                            'cuota', a.cuota,
                            'fecha', a.fecha,
                            'diasAtrasados', a.diasAtrasados,
                            'mora', a.mora
                        )
                    )
                    FROM
                        (
                            SELECT
                                a.id,
                                IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
                                a.numeroCuota,
                                a.capital,
                                a.interes,
                                a.cuota,
                                a.capitalRestante,
                                a.interesRestante,
                                a.capitalRestante + a.interesRestante as cuotaRestante,
                                a.fecha,
                                a.diasAtrasados,
                                a.mora
                                FROM   (
                                        SELECT
                                            a.id,
                                            a.numeroCuota,
                                            a.capital,
                                            a.interes,
                                            IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
                                            IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
                                            IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
                                            a.cuota,
                                            a.fecha,
                                            DATEDIFF(CURDATE(), a.fecha) diasAtrasados
                                        FROM amortizations a
                                        INNER JOIN loans l on l.id = a.idPrestamo
                                        LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
                                        WHERE a.idPrestamo = $idPrestamo
                                        GROUP BY a.id
                                    ) AS a
                                order by a.id
                        )
                    AS a
            ) AS amortizaciones
        FROM loans l 
        INNER JOIN customers c ON c.id = l.idCliente 
        INNER JOIN types t ON t.id = l.idTipoAmortizacion 
        LEFT JOIN types tp ON tp.id = l.idTipoPlazo 
        LEFT JOIN boxes b ON b.id = l.idCaja 
        LEFT JOIN documents d on d.id = c.idDocumento
        LEFT JOIN contacts co on co.id = c.idContacto
        WHERE l.id = $idPrestamo");

        $prestamo = (count($prestamo) == 0) ? null : $prestamo[0];
        if($prestamo != null)
            $prestamo->pagos = \App\Pay::customAll($prestamo->idEmpresa, $prestamo->id);

        return $prestamo;
    }

    public static function amortizaciones($idPrestamo){
        return \DB::select("
        SELECT
        a.id,
        IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
        a.numeroCuota,
        a.capital,
        a.interes,
        a.cuota,
        a.capitalRestante,
        a.interesRestante,
        a.capitalRestante + a.interesRestante as cuotaRestante,
        a.fecha,
        a.diasAtrasados,
        a.mora
        FROM   (
                SELECT
                    a.id,
                    a.numeroCuota,
                    a.capital,
                    a.interes,
                    IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
                    IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
                    IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
                    a.cuota,
                    a.fecha,
                    DATEDIFF(CURDATE(), a.fecha) diasAtrasados
                FROM amortizations a
                INNER JOIN loans l on l.id = a.idPrestamo
                LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
                WHERE a.idPrestamo = $idPrestamo
                GROUP BY a.id
            ) AS a
        order by a.id
        ");
    }



    public static function fechaProximoPago($idPrestamo)
    {
        //Modelo, foreign key, local key
        // return $this->hasOne('App\Job', 'id', 'idTrabajo');
        // \DB::select("
        // SELECT
        // IF(a.capital <= 0 AND a.interes <= 0, 'pagada', 'no') as status,
        // a.capital,
        // a.interes,
        // a.fecha,
        // a.diasAtrasados
        // FROM   (
        //         SELECT
        //             a.id,
        //             IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capital,
        //             IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interes,
        //             (SELECT mora(l.id, a.id)) as mora,
        //             a.fecha,
        //             DATEDIFF(CURDATE(), a.fecha) diasAtrasados
        //         FROM amortizations a
        //         INNER JOIN loans l on l.id = a.idPrestamo
        //         LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
        //         WHERE a.idPrestamo = $this->id
        //         GROUP BY a.id
        //     ) AS a
        // ");

        $cuota = \DB::select("
        SELECT
            a.id,
            IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
            a.numeroCuota,
            a.capital,
            a.interes,
            a.cuota,
            a.capitalRestante,
            a.interesRestante,
            a.capitalRestante + a.interesRestante as cuotaRestante,
            a.fecha,
            a.diasAtrasados,
            a.mora
            FROM   (
                    SELECT
                        a.id,
                        a.numeroCuota,
                        a.capital,
                        a.interes,
                        IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
                        IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
                        IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
                        a.cuota,
                        a.fecha,
                        DATEDIFF(CURDATE(), a.fecha) diasAtrasados
                    FROM amortizations a
                    INNER JOIN loans l on l.id = a.idPrestamo
                    LEFT JOIN paydetails pd on pd.idAmortizacion = a.id
                    WHERE a.idPrestamo = $idPrestamo
                    GROUP BY a.id
                ) AS a
            HAVING pagada = 0
            order by a.id asc 
            limit 1
        ");

        return (count($cuota) > 0) ? $cuota[0]->fecha : null;
    }
}
