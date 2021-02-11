<?php

namespace App\Http\Controllers;

use App\Pay;
use Illuminate\Http\Request;

class PayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $datos = request()->validate([
        //     'data.id' => '',
        //     'data.nombres' => '',
        //     'data.usuario' => '',
        //     'data.apiKey' => '',
        //     'data.idEmpresa' => '',
        // ])["data"];

        // // return Response::json([
        // //     "message" => $data["apiKey"]
        // // ], 404);

        // \App\Classes\Helper::validateApiKey($datos["apiKey"]);
        // \App\Classes\Helper::validatePermissions($datos, "Clientes", ["Ver"]);

        // return Response::json([
        //     'mensaje' => '',
        //     'ciudades' => \App\City::cursor(),
        //     'estados' => \App\State::cursor(),
        //     'clientes' => \App\Http\Resources\CustomerSmallResource::collection(\App\Customer::where("idEmpresa", $datos["idEmpresa"])->cursor()),
        // ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.pago' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Pagos", ["Guardar"]);

        $pago = Pay::updateOrCreate(
            ["id" => $datos["pago"]["id"]],
            [
                "idUsuario" => $datos["usuario"]["id"],
                "idEmpresa" => $datos["usuario"]["idEmpresa"],
                "idTipoPago" => $datos["pago"]["tipo"]["id"],
                "monto" => $datos["pago"]["monto"],
                "devuelta" => $datos["pago"]["devuelta"],
                "comentario" => $datos["pago"]["comentario"],
            ]
        );


        // foreach ($detalle as $datos["pago"]["detalle"]) {
        //     \App\Paydetail::updateOrCreate(
        //         ["idPago" => $pago->id, "idAmortizacion" => ]
                
        //     );
        // }

        

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
     * @param  \App\Pay  $pay
     * @return \Illuminate\Http\Response
     */
    public function show(Pay $pay)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pay  $pay
     * @return \Illuminate\Http\Response
     */
    public function edit(Pay $pay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pay  $pay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pay $pay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pay  $pay
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pay $pay)
    {
        //
    }
}
