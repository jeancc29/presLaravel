<?php

namespace App\Http\Controllers;

use App\Coin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.fechaDesde' => '',
            'data.fechaHasta' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Dashboard", ["Dashboard"]);

        $queryFechaPrestamo = "";
        $queryFechaPagos = "";
        $queryFechaGasto = "";
        if(isset($datos["fechaDesde"]) && isset($datos["fechaDesde"]) ){
            $queryFechaPrestamo = "AND loans.fecha BETWEEN {$datos['fechaDesde']} AND {$datos['fechaHasta']}";
            $queryFechaPagos = "AND p.fecha BETWEEN {$datos['fechaDesde']} AND {$datos['fechaHasta']}";
            $queryFechaGasto = "WHERE expenses.fecha BETWEEN {$datos['fechaDesde']} AND {$datos['fechaHasta']}";
        }
        
            $data = \DB::select("
            SELECT
                (SELECT COUNT(customers.id) FROM customers WHERE customers.estado = 1 AND customers.idEmpresa = {$datos['usuario']['idEmpresa']}) AS cantidadClientes,
                (SELECT COUNT(loans.id) FROM loans WHERE loans.status in (1, 2) AND loans.idEmpresa = {$datos['usuario']['idEmpresa']}) AS cantidadPrestamos,
                (SELECT SUM(loans.monto) FROM loans WHERE loans.status in (1, 2) AND loans.idEmpresa = {$datos['usuario']['idEmpresa']} $queryFechaPrestamo) AS totalPrestado,
                (SELECT SUM(loans.montoInteres) FROM loans WHERE loans.status in (1, 2) AND loans.idEmpresa = {$datos['usuario']['idEmpresa']} $queryFechaPrestamo) AS totalInteres,
                (
                    SELECT
                    JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'capital', pay.capital,
                                'interes', pay.interes,
                                'mora', pay.mora,
                                'mes', mes.mes,
                                'fecha', pay.mes
                            )
                        )

                    FROM    (
                            SELECT 
                                SUM(pd.capital) AS capital,
                                SUM(pd.interes) AS interes,
                                SUM(pd.mora) AS mora,
                                MONTH(p.fecha) AS mes
                            FROM paydetails AS pd
                            INNER JOIN pays AS p on p.id = pd.idPago
                            WHERE 
                                p.status = 1 
                                AND p.idEmpresa = {$datos['usuario']['idEmpresa']}
                                $queryFechaPagos 
                                       
                            GROUP BY MONTH(p.fecha)
                        ) AS pay
                        RIGHT JOIN(
                            select 1 as mes UNION
                            select 2 as mes UNION
                            select 3 as mes UNION
                            select 4 as mes UNION
                            select 5 as mes UNION
                            select 6 as mes UNION
                            select 7 as mes UNION
                            select 8 as mes UNION
                            select 9 as mes UNION
                            select 10 as mes UNION
                            select 11 as mes UNION
                            select 12 as mes
                        ) AS mes ON mes.mes = pay.mes
                ) as ingresospormeses,
                (
                    SELECT 
                        SUM(egresos.montoTotal)
                    FROM (
                        SELECT SUM(loans.monto) montoTotal FROM loans WHERE loans.status in(1, 2) $queryFechaPrestamo
                        UNION
                        SELECT SUM(expenses.monto) montoTotal FROM expenses $queryFechaGasto
                    ) egresos
                ) AS totalEgresos,
                (
                    SELECT SUM(pays.monto) FROM pays WHERE pays.status = 1 $queryFechaPagos
                ) AS totalIngresos,
                (SELECT SUM(paydetails.mora) FROM paydetails WHERE paydetails.idPago in(SELECT pays.id FROM pays WHERE pays.status = 1)) AS totalMora
        ");

        return Response::json([
            "mensaje" => "",
            "data" => count($data) > 0 ? $data[0] : null,
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coin  $coin
     * @return \Illuminate\Http\Response
     */
    public function show(Coin $coin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coin  $coin
     * @return \Illuminate\Http\Response
     */
    public function edit(Coin $coin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coin  $coin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coin $coin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coin  $coin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coin $coin)
    {
        //
    }
}
