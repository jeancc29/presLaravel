<?php

namespace App\Http\Controllers;

use App\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class ReceiptController extends Controller
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
        \App\Classes\Helper::validatePermissions($data, "Configuraciones", ["Recibo"]);

        return Response::json([
            "data" => Receipt::where("idEmpresa", $data["idEmpresa"])->first()
        ], 200);
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
            'data.copia' => '',
            'data.capital' => '',
            'data.mora' => '',
            'data.interes' => '',
            'data.descuento' => '',
            'data.capitalPendiente' => '',
            'data.balancePendiente' => '',
            'data.fechaProximoPago' => '',
            'data.formaPago' => '',
            'data.firma' => '',
            'data.mostrarCentavosRecibidos' => '',
        ])["data"];

        
        try {
            \DB::beginTransaction();
            \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Configuraciones", ["Recibo"]);
            

            $configuracion = Receipt::updateOrCreate(
                [
                    "id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]
                ],
                [
                    "copia" => $datos["copia"],
                    "capital" => $datos["capital"],
                    "mora" => $datos["mora"],
                    "interes" => $datos["interes"],
                    "descuento" => $datos["descuento"],
                    "capitalPendiente" => $datos["capitalPendiente"],
                    "balancePendiente" => $datos["balancePendiente"],
                    "fechaProximoPago" => $datos["fechaProximoPago"],
                    "formaPago" => $datos["formaPago"],
                    "firma" => $datos["firma"],
                    "mostrarCentavosRecibidos" => $datos["mostrarCentavosRecibidos"],
                ]
            );
            \DB::commit();

            return Response::json([
                "mensaje" => "se ha guardado correctamente",
                "data" => $configuracion
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            abort(404, $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function show(Receipt $receipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function edit(Receipt $receipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipt $receipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Receipt  $receipt
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipt $receipt)
    {
        //
    }
}
