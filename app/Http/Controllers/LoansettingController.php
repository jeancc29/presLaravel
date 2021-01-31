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

        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Configuraciones", ["Prestamo"]);

        return Response::json([
            "configuracionPrestamo" => Loansetting::where("idEmpresa", $data["idEmpresa"])->first()
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
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
            'data.garantia' => '',
            'data.gasto' => '',
            'data.desembolso' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Configuraciones", ["Prestamo"]);
        

        $configuracion = Loansetting::updateOrCreate(
            [
                "id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]
            ],
            [
                "garantia" => $datos["garantia"],
                "gasto" => $datos["gasto"],
                "desembolso" => $datos["desembolso"],
            ]
        );

        return Response::json([
            "mensaje" => "se ha guardado correctamente",
            "configuracionPrestamo" => $configuracion
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
