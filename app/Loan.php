<?php

namespace App;

use Carbon\Carbon;
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
        "capitalTotal",
        "interesTotal",
        "capitalPendiente",
        "interesPendiente",
        "numeroCuotasPagadas",
        "fechaProximoPago",
        "status",
        "idRuta",
        "cuota",
    ];

    public function user()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\User', 'id', 'idUsuario');
    }

    public function customer()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Customer', 'id', 'idCliente');
    }

    public function amortizations()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Amortization', 'idPrestamo');
    }

    public function pays()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Pay', 'idPrestamo');
    }

    public function type()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipo');
    }

    public function termType()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipoPlazo');
    }

    public function amortizationType()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Type', 'id', 'idTipoAmortizacion');
    }

    public function box()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Box', 'id', 'idCaja');
    }

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
                            LEFT JOIN (
                                SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = l.id AND pays.status = 1)
                            ) pd on pd.idAmortizacion = a.id
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

//        Loan::query()
//            ->selectRaw("")
//            ->join("")
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
            (SELECT JSON_OBJECT('id', tp.id, 'descripcion', tp.descripcion)) as tipoPlazo,
            (SELECT JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
            l.monto,
            l.porcentajeInteres,
            l.porcentajeInteresAnual,
            l.montoInteres,
            (select cuota FROM amortizations WHERE amortizations.idPrestamo = l.id LIMIT 1) as cuota,
            l.numeroCuotas,
            l.fecha,
            l.fechaPrimerPago,
            l.fechaProximoPago,
            (SELECT IF(b.id IS NOT NULL, JSON_OBJECT('id', b.id, 'descripcion', b.descripcion), null)) as caja,
            l.codigo,
            (
                SELECT
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', d.id,
                            'dia', d.dia,
                            'weekday', d.weekday
                        )
                    )
                FROM days d
                INNER JOIN daysexcludeds de ON de.idDia = d.id
                WHERE de.idPrestamo = l.id
            )AS diasExcluidos,
            l.porcentajeMora,
            l.diasGracia,
            l.capitalTotal as capitalTotal,
            l.interesTotal as interesTotal,
            l.capitalPendiente as capitalPendiente,
            l.interesPendiente as interesPendiente,
            l.numeroCuotasPagadas as numeroCuotasPagadas,
            (
                SELECT
                    IF(
                        uc.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', uc.id,
                            'nombres', uc.nombres,
                            'apellidos', uc.apellidos,
                            'usuario', uc.usuario
                        )
                    )
            ) AS cobrador,
            (
                SELECT
                    IF(
                        g.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', g.id,
                            'nombres', g.nombres,
                            'numeroIdentificacion', g.numeroIdentificacion,
                            'telefono', g.telefono,
                            'direccion', g.direccion
                        )
                    )
            ) AS garante,
            (
                SELECT
                    IF(
                        le.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', le.id,
                            'idPrestamo', le.idPrestamo,
                            'idTipo', le.idTipo,
                            'porcentaje', le.porcentaje,
                            'importe', le.importe,
                            'incluirEnElFinanciamiento', le.incluirEnElFinanciamiento,
                            'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = le.idTipo)
                        )
                    )
            ) AS gastoPrestamo,
            (
                SELECT
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', guarantees.id,
                            'descripcion', guarantees.descripcion,
                            'tasacion', guarantees.tasacion,
                            'matricula', guarantees.matricula,
                            'marca', guarantees.marca,
                            'modelo', guarantees.modelo,
                            'chasis', guarantees.chasis,
                            'estado', guarantees.estado,
                            'placa', guarantees.placa,
                            'anoFabricacion', guarantees.anoFabricacion,
                            'motorOSerie', guarantees.motorOSerie,
                            'cilindros', guarantees.cilindros,
                            'color', guarantees.color,
                            'numeroPasajeros', guarantees.numeroPasajeros,
                            'numeroPuertas', guarantees.numeroPuertas,
                            'fuerzaMotriz', guarantees.fuerzaMotriz,
                            'capacidadCarga', guarantees.capacidadCarga,
                            'placaAnterior', guarantees.placaAnterior,
                            'fechaExpedicion', guarantees.fechaExpedicion,
                            'foto', guarantees.foto,
                            'fotoMatricula', guarantees.fotoMatricula,
                            'fotoLicencia', guarantees.fotoLicencia,
                            'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = guarantees.idTipo),
                            'condicion', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = guarantees.idTipoCondicion)
                        )
                    )
                FROM guarantees
                WHERE guarantees.idPrestamo = l.id
            ) AS garantias,
            (
                SELECT
                    IF(
                        d.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', d.id,
                            'banco', (SELECT JSON_OBJECT('id', banks.id, 'descripcion', banks.descripcion) FROM banks WHERE banks.id = d.idBanco),
                            'cuenta', (SELECT JSON_OBJECT('id', accounts.id, 'descripcion', accounts.descripcion) FROM accounts WHERE accounts.id = d.idCuenta),
                            'bancoDestino', (SELECT JSON_OBJECT('id', banks.id, 'descripcion', banks.descripcion) FROM banks WHERE banks.id = d.idBancoDestino),
                            'cuentaDestino', d.cuentaDestino,
                            'montoBruto', d.montoBruto,
                            'montoNeto', d.montoNeto,
                            'numeroCheque', d.numeroCheque,
                            'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = d.idTipo)
                        )
                    )
            ) AS desembolso,
            0 AS balancePendiente,
            (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = l.idTipoAmortizacion) as tipoAmortizacion,
            l.status,
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
                            LEFT JOIN (
                                SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = l.id AND pays.status = 1)
                            ) pd on pd.idAmortizacion = a.id
                            WHERE a.idPrestamo = l.id
                            GROUP BY a.id
                        ) AS a
                    order by a.id
                ) AS a
                WHERE a.pagada = 0 AND a.diasAtrasados >= 0
            ) as cuotasAtrasadas,
            (
                SELECT
                IF(
                    r.id IS NULL,
                    NULL,
                    JSON_OBJECT('id', r.id, 'descripcion', r.descripcion)
                )
            ) as ruta
        FROM loans l
        INNER JOIN customers c ON c.id = l.idCliente
        INNER JOIN types t ON t.id = l.idTipoAmortizacion
        LEFT JOIN boxes b ON b.id = l.idCaja
        LEFT JOIN types tp ON tp.id = l.idTipoPlazo
        LEFT JOIN users uc ON uc.id = l.idCobrador
        LEFT JOIN guarantors g ON g.idPrestamo = l.id
        LEFT JOIN loanexpenses le ON le.idPrestamo = l.id
        LEFT JOIN disbursements d ON d.id = l.idDesembolso
        LEFT JOIN routes r ON r.id = l.idRuta
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
                                        LEFT JOIN (
                                            SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1)
                                        ) pd on pd.idAmortizacion = a.id
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
                LEFT JOIN (
                    SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1)
                ) pd on pd.idAmortizacion = a.id
                WHERE a.idPrestamo = $idPrestamo
                GROUP BY a.id
            ) AS a
        order by a.id
        ");
    }



    public static function getFechaProximoPago($idPrestamo)
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

//        $cuota = \DB::select("
//        SELECT
//            a.id,
//            IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
//            a.numeroCuota,
//            a.capital,
//            a.interes,
//            a.cuota,
//            a.capitalRestante,
//            a.interesRestante,
//            a.capitalRestante + a.interesRestante as cuotaRestante,
//            a.fecha,
//            a.diasAtrasados,
//            a.mora
//            FROM   (
//                    SELECT
//                        a.id,
//                        a.numeroCuota,
//                        a.capital,
//                        a.interes,
//                        IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
//                        IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
//                        IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
//                        a.cuota,
//                        a.fecha,
//                        DATEDIFF(CURDATE(), a.fecha) diasAtrasados
//                    FROM amortizations a
//                    INNER JOIN loans l on l.id = a.idPrestamo
//                    LEFT JOIN (
//                        SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1)
//                    ) pd on pd.idAmortizacion = a.id
//                    WHERE a.idPrestamo = $idPrestamo
//                    GROUP BY a.id
//                ) AS a
//            HAVING pagada = 0
//            order by a.id asc
//            limit 1
//        ");

        $cuotasPagadas = Amortization::where(["idPrestamo" => $idPrestamo, "pagada" => 0])->orderBy("id", "asc")->first();
//        return (count($cuota) > 0) ? $cuota[0]->fecha : null;
        return $cuotasPagadas != null ? $cuotasPagadas->fecha : null;
    }

    public static function updateDiasAtrasados($idPrestamo){
        $fechaCuotaNoPagada = Amortization::query()->select("fecha")->where(["idPrestamo" => $idPrestamo, "pagada" => 0])->orderBy("id")->first();
        $date = Carbon::parse($fechaCuotaNoPagada->fecha . ' 00:00:00');
        $now = new Carbon(Carbon::now()->toDateString() . " 00:00:00");

        //Si la diferencia $diff es un numero > 0 eso quiere decir que hay dias atrasados, de lo contrario no.
        $diff = $date->diffInDays($now, false);
        Loan::query()->whereId($idPrestamo)->update(["diasAtrasados" => $diff > 0 ? $diff : 0]);
    }

    public static function numeroCuotasPagadas($idPrestamo)
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

//        $cuota = \DB::select("
//        SELECT
//            COUNT(cuotasPagadas.id) as cuotas
//        FROM (
//            SELECT
//                a.id,
//                IF(a.capitalRestante <= 0 AND a.interesRestante <= 0 AND a.mora <= 0, 1, 0) as pagada,
//                a.numeroCuota,
//                a.capital,
//                a.interes,
//                a.cuota,
//                a.capitalRestante,
//                a.interesRestante,
//                a.capitalRestante + a.interesRestante as cuotaRestante,
//                a.fecha,
//                a.diasAtrasados,
//                a.mora
//                FROM   (
//                        SELECT
//                            a.id,
//                            a.numeroCuota,
//                            a.capital,
//                            a.interes,
//                            IF(sum(pd.capital) is NULL, a.capital, a.capital - sum(pd.capital)) AS capitalRestante,
//                            IF(sum(pd.interes) is NULL, a.interes, a.interes - sum(pd.interes)) AS interesRestante,
//                            IF(sum(pd.mora) is NULL, (SELECT mora(l.id, a.id)), (SELECT mora(l.id, a.id)) - sum(pd.mora)) as mora,
//                            a.cuota,
//                            a.fecha,
//                            DATEDIFF(CURDATE(), a.fecha) diasAtrasados
//                        FROM amortizations a
//                        INNER JOIN loans l on l.id = a.idPrestamo
//                        LEFT JOIN (
//                            SELECT * FROM paydetails WHERE paydetails.idPago in (SELECT pays.id FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1)
//                        ) pd on pd.idAmortizacion = a.id
//                        WHERE a.idPrestamo = $idPrestamo
//                        GROUP BY a.id
//                    ) AS a
//                HAVING pagada = 1
//                order by a.id asc
//            ) AS cuotasPagadas
//
//        ");

        $cuotasPagadas = Amortization::where(["idPrestamo" => $idPrestamo, "pagada" => 1])->count();
        return $cuotasPagadas;
    }

    public function getCuotasAtrasadas(){
        $now = Carbon::now();
        if($this->diasGracia > 0)
            $now->addDays($this->diasGracia);

        return Amortization::query()->where(["idPrestamo" => $this->id, "pagada" => 0])->where("fecha", "<=", $now->toDateString())->count();
    }

    public static function updatePendientes($idPrestamo){
        $loan = Loan::query()->find($idPrestamo);
        $cuotasAtrasadas = $loan->getCuotasAtrasadas();
        $fechaProximoPago = Loan::getFechaProximoPago($idPrestamo);
        $numeroCuotasPagadas = Loan::numeroCuotasPagadas($idPrestamo);
        Loan::updateDiasAtrasados($idPrestamo);

//        $fechaProximoPago = isset($fechaProximoPago) ? "l.fechaProximoPago = '$fechaProximoPago'" : "l.fechaProximoPago = null";

        $capitalInteresPendiente = Amortization::query()->selectRaw("sum(capitalPendiente) capitalPendiente, sum(interesPendiente) interesPendiente")->where(["idPrestamo" => $idPrestamo])->get();

        $estadoPrestamo = 0;

        //Status... Desactivado == 0, Pagado == 2, Activo == 1
        if($loan->status != 0)
            $estadoPrestamo = $capitalInteresPendiente[0]->capitalPendiente == 0 && $capitalInteresPendiente[0]->interesPendiente == 0 ? 2 : 1;

        $loan->capitalPendiente = $capitalInteresPendiente[0]->capitalPendiente;
        $loan->interesPendiente = $capitalInteresPendiente[0]->interesPendiente;
        $loan->numeroCuotasPagadas = $numeroCuotasPagadas;
        $loan->cuotasAtrasadas = $cuotasAtrasadas;
        if($fechaProximoPago != null)
            $loan->fechaProximoPago = "$fechaProximoPago";

        $loan->status = $estadoPrestamo;
        $loan->save();

//        \DB::select("
//            UPDATE loans AS l
//            LEFT JOIN (
//                SELECT
//                    SUM(pays.capital) AS capital,
//                    SUM(pays.interes) AS interes,
//                    pays.idPrestamo
//                FROM (
//                    SELECT
//                        SUM(paydetails.capital) AS capital,
//                        SUM(paydetails.interes) AS interes,
//                        pays.idPrestamo
//                    FROM (
//                        SELECT * FROM pays WHERE pays.idPrestamo = $idPrestamo AND pays.status = 1
//                    ) as pays
//                    INNER JOIN paydetails ON pays.id = paydetails.idPago
//                    GROUP BY pays.idPrestamo
//                    UNION
//                    SELECT
//                        0 AS capital,
//                        0 AS interes,
//                        $idPrestamo AS interes
//                ) as pays
//                GROUP BY pays.idPrestamo
//            ) pays on l.id = pays.idPrestamo
//            SET
//                l.capitalPendiente = l.capitalTotal - IF(pays.capital IS NOT NULL, pays.capital, 0),
//                l.interesPendiente = l.interesTotal - IF(pays.interes IS NOT NULL, pays.interes, 0),
//                l.numeroCuotasPagadas = $numeroCuotasPagadas,
//                l.status = IF(l.status = 0, l.status, IF(l.capitalTotal - IF(pays.capital IS NOT NULL, pays.capital, 0) <= 0 AND l.interesTotal - IF(pays.interes IS NOT NULL, pays.interes, 0) <= 0, 2, 1)),
//                $fechaProximoPago
//            WHERE l.id = $idPrestamo
//        ");

    }
}
