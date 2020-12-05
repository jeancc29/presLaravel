<?php

namespace App\Http\Controllers;

use App\Loansetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class LoansettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            "loansettings" => Loansetting::first()
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
        $data = request()->validate([
            'data.id' => '',
            'data.guarantee' => '',
            'data.expense' => '',
            'data.disbursement' => '',
        ])["data"];

        $configuracion = Loansetting::updateOrCreate(
            [
                "id" => $data["id"]
            ],
            [
                "guarantee" => $data["guarantee"],
                "gasto" => $data["gasto"],
                "disbursement" => $data["disbursement"],
            ]
        );

        return Response::json([
            "message" => "se ha guardado correctamente",
            "loansettings" => $configuracion
        ], 201);
 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loansetting  $loansetting
     * @return \Illuminate\Http\Response
     */
    public function show(Loansetting $loansetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loansetting  $loansetting
     * @return \Illuminate\Http\Response
     */
    public function edit(Loansetting $loansetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loansetting  $loansetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loansetting $loansetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loansetting  $loansetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loansetting $loansetting)
    {
        //
    }
}
