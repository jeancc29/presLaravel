<?php

namespace App\Http\Controllers;

use App\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            "prestamos" => Loan::cursor(),
            "tipos" => \App\Type::whereIn("renglon", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->cursor(),
            "cajas" => \App\Box::cursor(),
            "bancos" => \App\Bank::cursor(),
            "cuentas" => \App\Account::get(),
            "configuracionPrestamo" => \App\Loansetting::first()
        ]);
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
        $datos = request()->validate([
            'data.id' => '',
            'data.cliente' => '',
            'data.tipoPlazo' => '',
            'data.tipoAmortizacion' => '',
            'data.monto' => '',
            'data.porcentajeInteres' => '',
            'data.numeroCuotas' => '',
            'data.fecha' => '',
            'data.fechaPrimerPago' => '',
            'data.caja' => '',
            'data.codigo' => '',
            'data.porcentajeMora' => '',
            'data.diasGracia' => '',
            'data.cobrador' => '',
            'data.gasto' => '',
            'data.garante' => '',
            'data.diasExcluidos' => '',
            'data.desembolso' => '',
            'data.usuario' => '',
        ])["data"];

        return Response::json([
            "mensaje" => "se ha guardado correctamente",
            "datos" => $datos
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
