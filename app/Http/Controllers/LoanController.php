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
            "loans" => Loan::cursor(),
            "types" => \App\Type::whereIn("category", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->cursor(),
            "boxes" => \App\Box::cursor(),
            "banks" => \App\Bank::cursor(),
            "accounts" => \App\Account::get(),
            "days" => \App\Day::get(),
            "loansetting" => \App\Loansetting::first()
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
        $data = request()->validate([
            'data.id' => '',
            'data.customer' => '',
            'data.typeTerm' => '',
            'data.typeAmortization' => '',
            'data.amount' => '',
            'data.interestPercent' => '',
            'data.annualInteresPercent' => '',
            'data.quotas' => '',
            'data.date' => '',
            'data.firstPaymentDate' => '',
            'data.box' => '',
            'data.uniqueCode' => '',
            'data.daysExcludeds' => '',
            'data.penaltyPercent' => '',
            'data.daysOfGrace' => '',
            'data.collector' => '',
            'data.expense' => '',
            'data.guarantor' => '',
            'data.disbursement' => '',
            'data.user' => '',
        ])["data"];

        return Response::json([
            "message" => "se ha guardado correctamente",
            "data" => $data
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
