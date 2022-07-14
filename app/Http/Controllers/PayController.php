<?php

namespace App\Http\Controllers;

use App\Abonodetail;
use App\Amortization;
use App\Http\Resources\PayResource;
use App\Loan;
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
     * @param \Illuminate\Http\Request $request
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
            'data.esAbonoACapital' => '',
            'data.tipoAbono' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Pagos", ["Guardar"]);

        $prestamo = Loan::find($datos["idPrestamo"]);

        if($datos["esAbonoACapital"]){
            if($datos["tipoAbono"]["descripcion"] == "Disminuir plazo" && $prestamo->amortizationType->descripcion == "Capital al final")
                throw new \Exception("Advertancia: no puede disminuir plazos cuando el tipo de prestamo es de Capital al final");
        }

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
                "esAbonoACapital" => $datos["esAbonoACapital"],
                "idTipoAbonoACapital" => $datos["tipoAbono"] != null ? $datos["tipoAbono"]["descripcion"] : null
            ]
        );

        if($datos["esAbonoACapital"]){
//            $prestamo = Loan::find($datos["idPrestamo"]);
//            $prestamo->capitalPendiente = $prestamo->capitalPendiente - $datos["monto"];
//            $prestamo->save();

            $amortizacionesNoPagadas = $prestamo->amortizations()->wherePagada(0)->orderBy("id")->get();
            foreach ($amortizacionesNoPagadas as $a){
                Abonodetail::query()->create([
                    "numeroCuota" => $a->numeroCuota,
                    "idTipo" => $a->idTipo,
                    "idPrestamo" => $a->idPrestamo,
                    "idPago" => $data->id,
                    "cuota" => $a->cuota,
                    "interes" => $a->interes,
                    "capital" => $a->capital,
                    "capitalRestante" => $a->capitalRestante,
                    "capitalSaldado" => $a->capitalSaldado,
                    "interesSaldado" => $a->interesSaldado,
                    "fecha" => $a->fecha,
                    "capitalPendiente" => $a->capitalPendiente,
                    "interesPendiente" => $a->interesPendiente,
                    "moraPendiente" => $a->moraPendiente,
                    "pagada" => $a->pagada
                ]);
            }

            if($datos["tipoAbono"]["descripcion"] == "Disminuir plazo"){
                $amortizacionesAbono = Amortization::amortizar($prestamo->capitalPendiente - $datos["monto"], $prestamo->porcentajeInteres, $amortizacionesNoPagadas->count(), $prestamo->amortizationType, $prestamo->termType, null, null, $amortizacionesNoPagadas);
                $ids = $amortizacionesAbono->pluck("id");
                Amortization::query()->where("idPrestamo", $prestamo->id)->whereNotIn("id", $ids)->delete();
                foreach ($amortizacionesAbono as $amortization){
                    $amortization->save();
                }
                $prestamo->numeroCuotas = $prestamo->amortizations()->count();
                $prestamo->save();
            }else{
                $amortizacionesAbono = Amortization::amortizar($prestamo->capitalPendiente - $datos["monto"], $prestamo->porcentajeInteres, $amortizacionesNoPagadas->count(), $prestamo->amortizationType, $prestamo->termType);
                for($c=0; $c < $amortizacionesAbono->count(); $c++){
                    $amortizacionesNoPagadas[$c]->interes = $amortizacionesAbono[$c]["interes"];
                    $amortizacionesNoPagadas[$c]->capital = $amortizacionesAbono[$c]["capital"];
                    $amortizacionesNoPagadas[$c]->cuota = $amortizacionesAbono[$c]["cuota"];
                    $amortizacionesNoPagadas[$c]->capitalRestante = $amortizacionesAbono[$c]["capitalRestante"];
                    $amortizacionesNoPagadas[$c]->capitalSaldado = $amortizacionesAbono[$c]["capitalSaldado"];
                    $amortizacionesNoPagadas[$c]->interesSaldado = $amortizacionesAbono[$c]["interesSaldado"];
                    $amortizacionesNoPagadas[$c]->interesPendiente = $amortizacionesAbono[$c]["interes"];
                    $amortizacionesNoPagadas[$c]->capitalPendiente = $amortizacionesAbono[$c]["capital"];
                    $amortizacionesNoPagadas[$c]->save();
                }
            }

        }
        else
            foreach ($datos["detallepago"] as $detalle) {
                $detalle = \App\Paydetail::updateOrCreate(
                    ["idPago" => $data->id, "idAmortizacion" => $detalle["idAmortizacion"]],
                    [
                        "capital" => $detalle["capital"],
                        "interes" => $detalle["interes"],
                        "mora" => $detalle["mora"],
                    ]
                );

                //Actualizamos los metadatos(capitalPendiente, interesPendiente, moraPendiente, pagada) de la tabla amortizacion
    //            $a = Amortization::query()->where(["id" => $detalle["idAmortizacion"], "idPrestamo" => $datos["idPrestamo"]])->first();
    //            if($detalle["capital"] > 0)
    //                $a->capitalPendiente -= $detalle["capital"];
    //
    //            //Si el interes pagado == 0 eso quiere decir que el descuento establecido pagó el total del interes
    //            if($detalle["interes"] == 0)
    //                $a->interesPendiente = 0;
    //            elseif($detalle["interes"] > 0)
    //                $a->interesPendiente -= $detalle["interes"];
    //            //Si la mora pagado == 0 eso quiere decir que el descuento establecido pagó el total de la mora
    //            if($detalle["mora"] == 0)
    //                $a->moraPendiente = 0;
    //            elseif($detalle["mora"] > 0)
    //                $a->moraPendiente -= $detalle["mora"];
    //
    ////            if($a->capitalPendiente <= 0 && $a->interesPendiente <= 0 && $a->moraPendiente <= 0)
    //            $a->pagada = $a->capitalPendiente <= 0 && $a->interesPendiente <= 0 && $a->moraPendiente <= 0;
    //
    //            $a->save();
                Amortization::updatePendientes($datos["idPrestamo"], $detalle);
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
//            "data" => Pay::customFirst($data->id),
            "data" => new PayResource($data),
//            "prestamo" => \App\Loan::customFirstAmortizaciones($data->idPrestamo),
            "empresa" => new \App\Http\Resources\CompanyResource(\App\Company::where("idEmpresa", $datos["usuario"]["idEmpresa"])->first()),
            "configuracionRecibo" => \App\Receipt::where("idEmpresa", $datos["usuario"]["idEmpresa"])->first()
            // "capitalPendiente" => $prestamo->capitalPendiente,
            // "interesPendiente" => $prestamo->interesPendiente,
            // "status" => $prestamo->status,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Pay $pay
     * @return \Illuminate\Http\Response
     */
    public function show(Pay $pay)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Pay $pay
     * @return \Illuminate\Http\Response
     */
    public function edit(Pay $pay)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Pay $pay
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pay $pay)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Pay $pay
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
            if ($data != null) {
                $data->status = 0;
                $data->save();

                if($data->esAbonoACapital){
//                    if($data->capitalPaymentType->descripcion == 'Disminuir plazo'){
                        $loan = $data->loan;
                        $amortizationsFromAbonoDetail = $data->abonoDetail()->orderBy("numeroCuota")->get();

                        //Eliminamos las amortizaciones que no han sido pagadas, para luego crear las amortizaciones antiguas
                        $loan->amortizations()->wherePagada(0)->orderBy("id")->delete();

                        //Creamos las amortizaciones antiguas
                        foreach ($amortizationsFromAbonoDetail as $amortization) {
                            $a = \App\Amortization::Create(
                                [
                                    "idPrestamo" => $loan->id,
                                    "idTipo" => $loan->idTipoAmortizacion,
                                    "numeroCuota" => $amortization->numeroCuota,
                                    "cuota" => $amortization->cuota,
                                    "interes" => $amortization->interes,
                                    "capital" => $amortization->capital,
                                    "capitalRestante" => $amortization->capitalRestante,
                                    "capitalSaldado" => $amortization->capitalSaldado,
                                    "interesSaldado" => $amortization->interesSaldado,
                                    "fecha" => $amortization->fecha,
                                    "capitalPendiente" => $amortization->capital,
                                    "interesPendiente" => $amortization->interes,
                                ]
                            );
                            $a->calculateMora($loan);
                        }

                        $loan->numeroCuotas = $loan->amortizations()->count();
                        $loan->save();
//                    }else{
//
//                    }
                }

                if($data->esRenegociacion){
//                    if($data->capitalPaymentType->descripcion == 'Disminuir plazo'){
                    $loan = $data->loan;
                    $renegotiation = $data->renegotiation;

                    $loan->monto = $renegotiation->monto;
                    $loan->porcentajeInteres = $renegotiation->porcentajeInteres;
                    $loan->porcentajeInteresAnual = $renegotiation->porcentajeInteresAnual;
                    $loan->montoInteres = $renegotiation->montoInteres;
                    $loan->numeroCuotas = $renegotiation->numeroCuotas;
                    $loan->fecha = $renegotiation->fecha;
                    $loan->fechaPrimerPago = $renegotiation->fechaPrimerPago;
                    $loan->porcentajeMora = $renegotiation->porcentajeMora;
                    $loan->diasGracia = $renegotiation->diasGracia;
                    $loan->capitalTotal = $renegotiation->capitalTotal;
                    $loan->interesTotal = $renegotiation->interesTotal;
                    $loan->capitalPendiente = $renegotiation->capitalPendiente;
                    $loan->interesPendiente = $renegotiation->interesPendiente;
                    $loan->mora = $renegotiation->mora;
                    $loan->cuota = $renegotiation->cuota;
                    $loan->numeroCuotasPagadas = $renegotiation->numeroCuotasPagadas;
                    $loan->cuotasAtrasadas = $renegotiation->cuotasAtrasadas;
                    $loan->diasAtrasados = $renegotiation->diasAtrasados;
                    $loan->fechaProximoPago = $renegotiation->fechaProximoPago;
                    $loan->idTipoPlazo = $renegotiation->idTipoPlazo;
                    $loan->idTipoAmortizacion = $renegotiation->idTipoAmortizacion;
                    $loan->save();

                    $amortizationsFromRenegotiationDetail = $renegotiation->detail()->orderBy("numeroCuota")->get();

                    //Eliminamos las amortizaciones que no han sido pagadas, para luego crear las amortizaciones antiguas
                    $loan->amortizations()->wherePagada(0)->orderBy("id")->delete();

                    //Creamos las amortizaciones antiguas
                    foreach ($amortizationsFromRenegotiationDetail as $amortization) {
                        $a = \App\Amortization::Create(
                            [
                                "idPrestamo" => $loan->id,
                                "idTipo" => $loan->idTipoAmortizacion,
                                "numeroCuota" => $amortization->numeroCuota,
                                "cuota" => $amortization->cuota,
                                "interes" => $amortization->interes,
                                "capital" => $amortization->capital,
                                "capitalRestante" => $amortization->capitalRestante,
                                "capitalSaldado" => $amortization->capitalSaldado,
                                "interesSaldado" => $amortization->interesSaldado,
                                "fecha" => $amortization->fecha,
                                "capitalPendiente" => $amortization->capital,
                                "interesPendiente" => $amortization->interes,
                            ]
                        );
                        $a->calculateMora($loan);
                    }

                    $loan->numeroCuotas = $loan->amortizations()->count();
                    $loan->save();
//                    }else{
//
//                    }
                }

                $this->deleteTransaction($data->id);

                if(!$data->esAbonoACapital){
                    $detalle = $data->detail;
                    foreach ($detalle as $detail) {
                        Amortization::updatePendientes($data->idPrestamo, $detail, true);
                    }
                }
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

    public function deleteTransaction($idReferencia, $comentario = null)
    {
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Pago"])->first();
        \App\Transaction::cancel($tipo, $idReferencia, $comentario);
    }
}
