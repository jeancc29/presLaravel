<?php

namespace App\Http\Controllers;

use App\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class PayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        // return Response::json([
        //     "message" => $data["apiKey"]
        // ], 404);

        \App\Classes\Helper::validateApiKey($datos["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos, "Clientes", ["Ver"]);

        // return Response::json([
        //     'mensaje' => '',
        //     'ciudades' => \App\City::cursor(),
        //     'estados' => \App\State::cursor(),
        //     'clientes' => \App\Http\Resources\CustomerSmallResource::collection(\App\Customer::where("idEmpresa", $datos["idEmpresa"])->cursor()),
        // ], 201);
        return Response::json([
            'mensaje' => '',
            'rutas' => \App\Route::where("idEmpresa", $datos["idEmpresa"]),
            'usuarios' => \App\User::where("idEmpresa", $datos["idEmpresa"]),
            'cajas' => \App\User::customCajas($datos),
            'data' => \App\Pay::customAll($datos["idEmpresa"]),
        ], 200);
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
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
            'data.idPrestamo' => '',
            'data.idCliente' => '',
            'data.monto' => '',
            'data.descuento' => '',
            'data.devuelta' => '',
            'data.comentario' => '',
            'data.concepto' => '',
            'data.caja' => '',
            'data.tipoPago' => '',
            'data.detallepago' => '',
            'data.fecha' => '',
            'data.capitalPagado' => '',
            'data.interesPagado' => '',
            'data.numeroCuotaPagada' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Pagos", ["Guardar"]);

        $data = Pay::updateOrCreate(
            ["id" => $datos["id"]],
            [
                "idUsuario" => $datos["usuario"]["id"],
                "idEmpresa" => $datos["usuario"]["idEmpresa"],
                "idTipoPago" => $datos["tipoPago"]["id"],
                "idCliente" => $datos["idCliente"],
                "idPrestamo" => $datos["idPrestamo"],
                "monto" => $datos["monto"],
                "descuento" => $datos["descuento"],
                "devuelta" => $datos["devuelta"],
                "comentario" => $datos["comentario"],
                "concepto" => $datos["concepto"],
                "fecha" => $datos["fecha"],
            ]
        );

        foreach ($datos["detallepago"] as $detalle) {
            $detalle = \App\Paydetail::updateOrCreate(
                ["idPago" => $data->id, "idAmortizacion" => $detalle["idAmortizacion"]],
                [
                    "capital" => $detalle["capital"],
                    "interes" => $detalle["interes"],
                    "mora" => $detalle["mora"],
                ]
            );
        }

        // $prestamo = \App\Loan::whereId($data->idPrestamo)->first();
        // $prestamo->capitalPendiente = $prestamo->capitalPendiente - $datos["capitalPagado"];
        // $prestamo->interesPendiente = $prestamo->interesPendiente - $datos["interesPagado"];
        // $prestamo->numeroCuotasPagadas = $datos["numeroCuotaPagada"];
        // $prestamo->fechaProximoPago = \App\Loan::fechaProximoPago($prestamo->id);
        // if($prestamo->capitalPendiente <= 0 && $prestamo->interesPendiente <= 0)
        //     $prestamo->status = 2;

        // $prestamo->save();
        // $prestamo->amortizaciones = \App\Loan::amortizaciones($prestamo->id);

        \App\Loan::updatePendientes($data->idPrestamo);

        $tipo = \App\Classes\Helper::stdClassToArray(\App\Type::where(["descripcion" => "Pago", "renglon" => "transaccion"])->first());
        \App\Transaction::make($datos["usuario"], $datos["caja"], $data->monto, $tipo, $data->id, $datos["concepto"]);

        return Response::json([
            "data" => Pay::customFirst($data->id),
            "prestamo" => \App\Loan::customFirstAmortizaciones($data->idPrestamo),
            // "capitalPendiente" => $prestamo->capitalPendiente,
            // "interesPendiente" => $prestamo->interesPendiente,
            // "status" => $prestamo->status,
        ]);
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
        $datos = request()->validate([
            'data.usuario' => '',
            'data.pago' => '',
        ])["data"];

        try {
            \DB::beginTransaction();
            // \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Pagos", ["Eliminar"]);
    
            $data = Pay::whereId([$datos["pago"]["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]])->first();
            if($data != null){
                $data->status = 0;
                $data->save();
                $this->deleteTransaction($data->id);
            }

            \App\Loan::updatePendientes($data->idPrestamo);
    
            \DB::commit();
            return Response::json([
                "mensaje" => "Se ha eliminado correctamente",
                "data" => $data,
                "prestamo" => \App\Loan::customFirstAmortizaciones($data->idPrestamo)
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            abort(402, $th->getMessage());
        }
    }

    public function deleteTransaction($idReferencia, $comentario = null){
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Pago"])->first();
        \App\Transaction::cancel($tipo, $idReferencia, $comentario);
    }
}
