<?php

namespace App\Http\Controllers;

use App\Amortization;
use App\Box;
use App\Coin;
use App\Company;
use App\Http\Resources\LoanResource;
use App\Http\Resources\LoanWithAmortizationResource;
use App\Http\Resources\TypeResource;
use App\Loan;
use App\Pay;
use App\Paydetail;
use App\Renegotiation;
use App\Renegotiationdetail;
use App\Type;
use Carbon\Carbon;
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
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
            'data.idPrestamo' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Prestamos", ["Ver"]);

        $idEmpresa = $data["idEmpresa"];

        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        $prestamos = LoanResource::collection(Loan::query()->where("idEmpresa", $idEmpresa)->get());

        return Response::json([
            "prestamos" => $prestamos,
            "tipos" =>  TypeResource::collection(\App\Type::whereIn("renglon", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->get()),
        ]);
    }

    public function indexAdd()
    {
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
            'data.idPrestamo' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Prestamos", ["Ver"]);

        $idEmpresa = $data["idEmpresa"];
        $company = Company::query()->find($idEmpresa);

        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        // $prestamos = \DB::select("select
        // l.id,
        // (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
        // l.monto,
        // l.porcentajeInteres,
        // (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        // l.numeroCuotas,
        // l.monto as balancePendiente,
        // l.monto as capitalPendiente,
        // l.created_at fechaProximoPago,
        // (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        // l.codigo codigo,
        // (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja
        // from loans l
        //  inner join customers c on c.id = l.idCliente
        //  inner join types t on t.id = l.idTipoAmortizacion
        //  left join boxes b on b.id = l.idCaja
        // where l.idEmpresa = $idEmpresa
        //  limit 50 ");
        //  where l.created_at between '{$fechaInicial}' and '{$fechaFinal}' limit 50 ");
        $prestamo = (isset($data["idPrestamo"])) ? Loan::customFirst($data["idPrestamo"]) : null;

        return Response::json([
            "data" => $prestamo,
            "tipos" => \App\Type::whereIn("renglon", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->cursor(),
            "cajas" => \App\Box::where("idEmpresa", $idEmpresa)->cursor(),
            "bancos" => \App\Bank::where("idEmpresa", $idEmpresa)->cursor(),
            "cuentas" => \App\Account::where("idEmpresa", $idEmpresa)->get(),
            "dias" => \App\Day::get(),
            "configuracionPrestamo" => \App\Loansetting::where("idEmpresa", $idEmpresa)->first(),
            "rutas" => \App\Route::where("idEmpresa", $idEmpresa)->get(),
            "cobradores" => \App\User::customAll($idEmpresa, \App\Role::whereDescripcion("Cobrador")->first()->id),
            "monedas" => Coin::query()
                ->select("id", "nombre")
                ->when($company != null, function($q) use($company){ $q->orderByRaw("FIELD(id, {$company->idMoneda}) DESC"); })
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // \DB::select("select l.id, (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos)) as cliente, l.monto, l.porcentajeInteres, (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota, l.numeroCuotas, l.monto as balancePendiente, l.monto as capitalPendiente, l.created_at fechaProximoPago, (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion, l.codigo codigo, (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja from loans l inner join customers c on c.id = l.idCliente inner join types t on t.id = l.idTipoAmortizacion inner join boxes b on b.id = l.idCaja limit 50 ");
    }

    public function validateDataCanBeEdited($datos){
        /// Fields like monto, porcentajeInteres, numeroCuotas can be edited only if there arent payment made it
        $errorMessage = null;
        $prestamo = Loan::whereId($datos["id"])->first();
        if($prestamo == null)
            return;

        if(\App\Pay::exists($prestamo->id)){
            if($datos["monto"] != $prestamo->monto)
                $errorMessage = "El monto no se puede editar porque ya hay pagos realizados";
            else if($datos["numeroCuotas"] != $prestamo->numeroCuotas)
                $errorMessage = "Las cuotas no se pueden editar porque ya hay pagos realizados";
            else if($datos["porcentajeInteresAnual"] != $prestamo->porcentajeInteresAnual)
                $errorMessage = "El interes no se puede editar porque ya hay pagos realizados {$datos["porcentajeInteres"]} == {$prestamo->porcentajeInteres}";
            else if($datos["fechaPrimerPago"] != $prestamo->fechaPrimerPago)
                $errorMessage = "La fecha primer pago no se puede editar porque ya hay pagos realizados";
            else
                return;
        }else{
            return;
        }

        abort(402, $errorMessage);
    }

    public function testCustomFirst(){
        $datos = request()->validate([
            'data.id' => '',
        ])["data"];


        return Response::json([
            "data" => \App\Loan::customFirst($datos["id"])
        ]);
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
            'data.cliente' => '',
            'data.tipoPlazo' => '',
            'data.tipoAmortizacion' => '',
            'data.monto' => '',
            'data.porcentajeInteres' => '',
            'data.porcentajeInteresAnual' => '',
            'data.montoInteres' => '',
            'data.numeroCuotas' => '',
            'data.fecha' => '',
            'data.fechaPrimerPago' => '',
            'data.caja' => '',
            'data.codigo' => '',
            'data.diasExcluidos' => '',
            'data.porcentajeMora' => '',
            'data.diasGracia' => '',
            'data.capitalTotal' => '',
            'data.interesTotal' => '',
            'data.capitalPendiente' => '',
            'data.interesPendiente' => '',
            'data.fechaProximoPago' => '',
            'data.cobrador' => '',
            'data.gastoPrestamo' => '',
            'data.garante' => '',
            'data.garantias' => '',
            'data.desembolso' => '',
            'data.usuario' => '',
            'data.amortizaciones' => '',
            'data.ruta' => '',
            'data.esRenegociacion' => '',
        ])["data"];



        // return Response::json([
        //     "message" => "La caja no tiene monto suficiente. {$datos["caja"]["balance"]}",
        // ], 404);

        $amortizationsNeedToUpdate = false;
        $prestamo = Loan::query()->find($datos["id"]);
        $montoEntregadoDeLaRenegociacion = 0;

//        $ddd = collect($datos["diasExcluidos"])->implode("weekday");
//        abort(404, "Holaaa: " . $ddd . " hey: " . Carbon::now()->dayOfWeek);

        // \DB::transaction(function() use($datos){

            // return Response::

//            try {
                \DB::beginTransaction();

                $caja = isset($datos["caja"]) ? Box::find($datos["caja"]["id"]) : null;

                /// VALIDATE DATA
                \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
                \App\Classes\Helper::validatePermissions($datos["usuario"], "Prestamos", ["Guardar"]);
                \App\Box::validateMonto($caja, $datos["monto"]);

                /// VALIDATE DATA TO EDIT
                if(!$datos["esRenegociacion"])
                $this->validateDataCanBeEdited($datos);

                if($prestamo != null){
                    if($prestamo->id != $datos["id"] || ($prestamo->id == $datos["id"] && ($prestamo->numeroCuotas != $datos["numeroCuotas"] || $prestamo->porcentajeInteres != $datos["porcentajeInteres"] || $prestamo->idTipoAmortizacion != $datos["tipoAmortizacion"]["id"] || $prestamo->idTipoPlazo != $datos["tipoPlazo"]["id"])))
                        $amortizationsNeedToUpdate = true;

                    if($datos["esRenegociacion"]) {
                        $montoEntregadoDeLaRenegociacion = $datos["monto"] - $prestamo->capitalPendiente;
                        $amortizationsNeedToUpdate = true;

                        if($datos["monto"] <= $prestamo->capitalPendiente)
                            throw new \Exception("El monto a renegociar debe ser mayor que el capital pendiente del prestamo");

                        $renegotiation = Renegotiation::query()->create(
//                            ["idPrestamo" => $prestamo->id],
                            [
                                "idPrestamo" => $prestamo->id,
                                "idUsuario" => $datos["usuario"]["id"],
                                "monto" => $prestamo->monto,
                                "porcentajeInteres" => $prestamo->porcentajeInteres,
                                "porcentajeInteresAnual" => $prestamo->porcentajeInteresAnual,
                                "montoInteres" => $prestamo->montoInteres,
                                "numeroCuotas" => $prestamo->numeroCuotas,
                                "fecha" => $prestamo->fecha,
                                "fechaPrimerPago" => $prestamo->fechaPrimerPago,
                                "porcentajeMora" => $prestamo->porcentajeMora,
                                "diasGracia" => $prestamo->diasGracia,
                                "capitalTotal" => $prestamo->capitalTotal,
                                "interesTotal" => $prestamo->interesTotal,
                                "capitalPendiente" => $prestamo->capitalPendiente,
                                "interesPendiente" => $prestamo->interesPendiente,
                                "mora" => $prestamo->mora,
                                "cuota" => $prestamo->cuota,
                                "numeroCuotasPagadas" => $prestamo->numeroCuotasPagadas,
                                "cuotasAtrasadas" => $prestamo->cuotasAtrasadas,
                                "diasAtrasados" => $prestamo->diasAtrasados,
                                "fechaProximoPago" => $prestamo->fechaProximoPago,
                                "idTipoPlazo" => $prestamo->idTipoPlazo,
                                "idTipoAmortizacion" => $prestamo->idTipoAmortizacion
                            ]
                        );

                        $idsPagos = $prestamo->pays()->where("status", "!=", 0)->where("esAbonoACapital", 0)->where("esRenegociacion", 0)->get()->pluck("id");
                        $amortizations = $prestamo->amortizations()->when(count($idsPagos) > 0, function($q) use($idsPagos){
                            $q->whereNotIn("id", Paydetail::query()->whereIn("idPago", $idsPagos)->select("idAmortizacion")->pluck("idAmortizacion"));
                        })->get();

                        foreach ($amortizations as $amortization) {
                            Renegotiationdetail::query()->create(
//                                ["idPrestamo" => $prestamo->id, "idRenegociacion" => $renegotiation->id, "numeroCuota" => $amortization->numeroCuota],
                                [
                                    "idRenegociacion" => $renegotiation->id,
                                    "idPrestamo" => $prestamo->id,
                                    "numeroCuota" => $amortization->numeroCuota,
                                    "cuota" => $amortization->cuota,
                                    "interes" => $amortization->interes,
                                    "capital" => $amortization->capital,
                                    "mora" => $amortization->mora,
                                    "capitalRestante" => $amortization->capitalRestante,
                                    "capitalSaldado" => $amortization->capitalSaldado,
                                    "interesSaldado" => $amortization->interesSaldado,
                                    "capitalPendiente" => $amortization->capitalPendiente,
                                    "interesPendiente" => $amortization->interesPendiente,
                                    "moraPendiente" => $amortization->moraPendiente,
                                    "pagada" => $amortization->pagada,
                                    "fecha" => $amortization->fecha,
                                    "idTipo" => $amortization->idTipo,
                                ]
                            );
                        }

                        Pay::query()->updateOrCreate(
                            ["idRenegociacion" => $renegotiation->id],
                            [
                                "idUsuario" => $datos["usuario"]["id"],
                                "idCliente" => $prestamo->idCliente,
                                "idPrestamo" => $prestamo->id,
                                "idEmpresa" => $prestamo->idEmpresa,
                                "idTipoPago" => $datos["desembolso"]["tipo"]["id"],
                                "monto" => $montoEntregadoDeLaRenegociacion,
                                "concepto" => "REENGANCHE",
                                "idRenegociacion" => $renegotiation->id,
                                "fecha" => Carbon::now()->toDateString(),
                                "esRenegociacion" => 1
                            ]
                        );
                    }
                }else
                    $amortizationsNeedToUpdate = true;

                /// BEGIN THE PROCESS TO CREATE
                $desembolso = \App\Disbursement::updateOrCreate(
                    ["id" => $datos["desembolso"]["id"]],
                    [
                        "idTipo" => $datos["desembolso"]["tipo"]["id"],
                        "idBanco" => ($datos["desembolso"]["banco"] != null) ? $datos["desembolso"]["banco"]["id"] : null,
                        "idCuenta" => ($datos["desembolso"]["cuenta"] != null) ? $datos["desembolso"]["cuenta"]["id"] : null,
                        "idBancoDestino" => ($datos["desembolso"]["bancoDestino"] != null) ? $datos["desembolso"]["bancoDestino"]["id"] : null,
                        "cuentaDestino" => $datos["desembolso"]["cuentaDestino"],
                        "numeroCheque" => $datos["desembolso"]["numeroCheque"],
                        "montoBruto" => $datos["desembolso"]["montoBruto"],
                        "montoNeto" => $datos["desembolso"]["montoNeto"],
                    ]
                );


                $prestamo = Loan::updateOrCreate(
                    [
                        "id" => $datos["id"]
                    ],
                    [
                        "monto" => $datos["monto"],
                        "porcentajeInteres" => $datos["porcentajeInteres"],
                        "porcentajeInteresAnual" => $datos["porcentajeInteresAnual"],
                        "montoInteres" => $datos["montoInteres"],
                        "numeroCuotas" => $datos["numeroCuotas"],
                        "fecha" => $datos["fecha"],
                        "fechaPrimerPago" => $datos["fechaPrimerPago"],
                        "codigo" => $datos["codigo"],
                        "porcentajeMora" => $datos["porcentajeMora"],
                        "diasGracia" => $datos["diasGracia"],
                        "capitalTotal" => $datos["capitalTotal"],
                        "interesTotal" => $datos["interesTotal"],
                        "capitalPendiente" => $datos["esRenegociacion"] ? $datos["monto"] : $datos["capitalPendiente"],
                        "interesPendiente" => $datos["interesPendiente"],
                        "fechaProximoPago" => $datos["fechaProximoPago"],
                        // "idUsuario" => $datos["usuario"]["id"],
                        "idEmpresa" => $datos["usuario"]["idEmpresa"],
                        "idUsuario" => $datos["usuario"]["id"],
                        "idCliente" => $datos["cliente"]["id"],
                        "idTipoPlazo" => $datos["tipoPlazo"]["id"],
                        "idTipoAmortizacion" => $datos["tipoAmortizacion"]["id"],
                        "idCaja" => ($datos["caja"] != null) ? $datos["caja"]["id"] : null,
                        "idRuta" => ($datos["ruta"] != null) ? $datos["ruta"]["id"] : null,
                        "idCobrador" => ($datos["cobrador"] != null) ? $datos["cobrador"]["id"] : null,
                        // "idCobrador" => $datos["cobrador"]["id"],
                        // "idGasto" => $gasto->id,
                        "idDesembolso" => $desembolso->id,
                    ]
                    );

                    if($datos["gastoPrestamo"] != null){
                        $gasto = \App\Loanexpense::updateOrCreate(
                            ["id" => $datos["gastoPrestamo"]["id"]],
                            [
                                "idPrestamo" => $prestamo->id,
                                "idTipo" => $datos["gastoPrestamo"]["tipo"]["id"],
                                "porcentaje" => $datos["gastoPrestamo"]["porcentaje"],
                                "importe" => $datos["gastoPrestamo"]["importe"],
                                "incluirEnElFinanciamiento" => $datos["gastoPrestamo"]["incluirEnElFinanciamiento"],
                            ]
                        );
                    }

                    if($datos["garante"] != null){
                        $garante = \App\Guarantor::updateOrCreate(
                            ["id" => $datos["garante"]["id"]],
                            [
                                "nombres" => $datos["garante"]["nombres"],
                                "telefono" => $datos["garante"]["telefono"],
                                "numeroIdentificacion" => $datos["garante"]["numeroIdentificacion"],
                                "direccion" => $datos["garante"]["direccion"],
                                "idPrestamo" => $prestamo->id,
                            ]
                        );
                    }

//                    \App\Amortization::where("id", ">", 0)->where("idPrestamo", $prestamo->id)->delete();
//                    foreach ($datos["amortizaciones"] as $amortizacion) {
//                        $montoDeLaCuotaDelPrestamo = $amortizacion["cuota"];
//                        $a = \App\Amortization::updateOrCreate(
//                            ["id" => $amortizacion["id"]],
//                            [
//                                "idPrestamo" => $prestamo->id,
//                                "idTipo" => $amortizacion["tipo"]["id"],
//                                "numeroCuota" => $amortizacion["numeroCuota"],
//                                "cuota" => $amortizacion["cuota"],
//                                "interes" => $amortizacion["interes"],
//                                "capital" => $amortizacion["capital"],
//                                "capitalRestante" => $amortizacion["capitalRestante"],
//                                "capitalSaldado" => $amortizacion["capitalSaldado"],
//                                "interesSaldado" => $amortizacion["interesSaldado"],
//                                "fecha" => $amortizacion["fecha"],
//                                "capitalPendiente" => $amortizacion["capital"],
//                                "interesPendiente" => $amortizacion["interes"],
//                            ]
//                        );
//                        $a->calculateMora($prestamo);
//                    }

                    if($amortizationsNeedToUpdate){
                        $idsPagos = $prestamo->pays()->where("status", "!=", 0)->where("esAbonoACapital", 0)->where("esRenegociacion", 0)->get()->pluck("id");
                        $prestamo->amortizations()->when(count($idsPagos) > 0, function($q) use($idsPagos){
                            $q->whereNotIn("id", Paydetail::query()->whereIn("idPago", $idsPagos)->select("idAmortizacion")->pluck("idAmortizacion"));
                        })->delete();
                        $amortizationCollection = collect();
                        $diasExcluidos = count($datos["diasExcluidos"]) == 0 ? collect() : collect($datos["diasExcluidos"])->map(function($d){ return $d;});
                        $tipoPlazo = Type::find($datos["tipoPlazo"]["id"]);
                        $tipoAmortizacion = Type::find($datos["tipoAmortizacion"]["id"]);
                        $fechaPrimerPago = $prestamo->id == $datos["id"] ? new Carbon($prestamo->fechaProximoPago) : new Carbon($datos["fechaPrimerPago"]);
                        $montoAmortizacion = $prestamo->id == $datos["id"] && !$datos["esRenegociacion"] ? $prestamo->capitalPendiente : $prestamo->monto;

                        $amortizationCollection = Amortization::amortizar($montoAmortizacion, $prestamo->porcentajeInteres, $prestamo->numeroCuotas, $tipoAmortizacion, $tipoPlazo, $fechaPrimerPago, $diasExcluidos);
                        $prestamo->cuota = $amortizationCollection[0]["cuota"];
                        $prestamo->save();

                        foreach ($amortizationCollection as $amortizacion) {
                            $montoDeLaCuotaDelPrestamo = $amortizacion["cuota"];
                            $a = \App\Amortization::Create(
//                            ["id" => $amortizacion["id"]],
                                [
                                    "idPrestamo" => $prestamo->id,
                                    "idTipo" => $datos["tipoAmortizacion"]["id"],
                                    "numeroCuota" => $amortizacion["numeroCuota"],
                                    "cuota" => $amortizacion["cuota"],
                                    "interes" => $amortizacion["interes"],
                                    "capital" => $amortizacion["capital"],
                                    "capitalRestante" => $amortizacion["capitalRestante"],
                                    "capitalSaldado" => $amortizacion["capitalSaldado"],
                                    "interesSaldado" => $amortizacion["interesSaldado"],
                                    "fecha" => $amortizacion["fecha"],
                                    "capitalPendiente" => $amortizacion["capital"],
                                    "interesPendiente" => $amortizacion["interes"],
                                ]
                            );
                            $a->calculateMora($prestamo);
                        }

                    }

                foreach ($datos["garantias"] as $garantia) {
                        \App\Guarantee::updateOrCreate(
                            ["id" => $garantia["id"]],
                            [
                                "tasacion" => $garantia["tasacion"],
                                "descripcion" => $garantia["descripcion"],
                                "marca" => $garantia["marca"],
                                "modelo" => $garantia["modelo"],
                                "chasis" => $garantia["chasis"],
                                "estado" => $garantia["estado"],
                                "placa" => $garantia["placa"],
                                "anoFabricacion" => $garantia["anoFabricacion"],
                                "motorOSerie" => $garantia["motorOSerie"],
                                "cilindros" => $garantia["cilindros"],
                                "color" => $garantia["color"],
                                "numeroPasajeros" => $garantia["numeroPasajeros"],
                                "numeroPuertas" => $garantia["numeroPuertas"],
                                "fuerzaMotriz" => $garantia["fuerzaMotriz"],
                                "capacidadCarga" => $garantia["capacidadCarga"],
                                "placaAnterior" => $garantia["placaAnterior"],
                                "fechaExpedicion" => $garantia["fechaExpedicion"],
                                "foto" => null,
                                "fotoMatricula" => null,
                                "fotoLicencia" => null,
                                "idPrestamo" => $prestamo->id,
                                "idEmpresa" => $prestamo->idEmpresa,
                                "idTipoCondicion" => $garantia["condicion"]["id"],
                                "idTipo" => $garantia["tipo"]["id"],
                            ]
                            );
                    }

                    if(!isset($datos["id"]) || ($prestamo->id == $datos["id"] && $datos["esRenegociacion"])) {
                        $tipo = \App\Classes\Helper::stdClassToArray(\App\Type::where(["descripcion" => "Préstamo", "renglon" => "transaccion"])->first());
                        $montoTransaction = !$datos["esRenegociacion"] ? $prestamo->monto : $montoEntregadoDeLaRenegociacion;
                        \App\Transaction::make($datos["usuario"], $caja, $montoTransaction, $tipo, $prestamo->id, "Desembolso de Préstamo", $datos["desembolso"]["tipo"]["id"]);
                    }

                    \DB::commit();

                    Loan::updatePendientes($prestamo->id);

                    return Response::json([
                        "mensaje" => "se ha guardado correctamente",
                        // "nalga" => "{$lastPrestamo->id}",
                        // "datos" => $datos,
//                        "prestamo" => Loan::customFirst($prestamo->id)
                    ]);
//            } catch (\Throwable $th) {
//                //throw $th;
//                \DB::rollback();
//                abort(402, $th->getMessage());
//            }
        // });
        // $lastPrestamo = Loan::latest('id')->where("idEmpresa", $datos["usuario"]["idEmpresa"])->first();
        // // $prestamo = \DB::select("select
        // // l.id,
        // // (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
        // // l.monto,
        // // l.porcentajeInteres,
        // // (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        // // l.numeroCuotas,
        // // l.monto as balancePendiente,
        // // l.monto as capitalPendiente,
        // // l.created_at fechaProximoPago,
        // // (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        // // l.codigo codigo,
        // // (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja
        // // from loans l
        // //  inner join customers c on c.id = l.idCliente
        // //  inner join types t on t.id = l.idTipoAmortizacion
        // //  left join boxes b on b.id = l.idCaja
        // //  where l.id = {$lastPrestamo->id}
        // //  limit 1 ");
        // $prestamo = Loan::customFirst($lastPrestamo->id);



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
        ])["data"];

//        throw new \Exception("Hola: {$datos["id"]}");

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Prestamos", ["Guardar"]);

        // $prestamo = \DB::select("select
        // l.id,
        // (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto, 'documento', (SELECT JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)), 'contacto', (SELECT JSON_OBJECT('id', co.id, 'celular', co.celular, 'correo', co.correo)))) as cliente,
        // l.monto,
        // l.porcentajeInteres,
        // (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        // l.numeroCuotas,
        // l.monto as balancePendiente,
        // l.monto as capitalPendiente,
        // l.created_at fechaProximoPago,
        // (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        // l.codigo codigo,
        // (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja,
        // (select JSON_ARRAYAGG(JSON_OBJECT('id', amortizations.id, 'capital', amortizations.capital, 'interes', amortizations.interes, 'cuota', amortizations.cuota, 'fecha', amortizations.fecha)) from amortizations where amortizations.idPrestamo = l.id) as amortizaciones
        // from loans l
        //  inner join customers c on c.id = l.idCliente
        //  inner join types t on t.id = l.idTipoAmortizacion
        //  left join boxes b on b.id = l.idCaja
        //  left join documents d on d.id = c.idDocumento
        //  left join contacts co on co.id = c.idContacto

        //  where l.id = {$datos['id']} and l.idEmpresa = {$datos['usuario']['idEmpresa']}
        //  limit 1 ");

//        $prestamo = Loan::customFirstAmortizaciones($datos['id']);
        $prestamo = Loan::query()->find($datos['id']);
        if($prestamo == null)
            throw new \Exception("El prestamo no existe");

        $usuario = \App\User::whereId($datos["usuario"]["id"])->first();
        $cajas = $usuario->cajas;
        if(count($cajas) == 0)
            $cajas = \App\Box::where("idEmpresa", $usuario->idEmpresa)->get();

         return Response::json([
            "prestamo" => new LoanWithAmortizationResource($prestamo),
            "tipos" => \App\Type::query()->whereIn("renglon", ["desembolso", "abonoCapital"])->where("descripcion", "!=", "Efectivo en ruta")->cursor(),
            "cajas" => $cajas,
            "empresa" => new \App\Http\Resources\CompanyResource(\App\Company::where("idEmpresa", $usuario->idEmpresa)->first()),
            "configuracionRecibo" => \App\Receipt::where("idEmpresa", $usuario->idEmpresa)->first()
         ]);
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
        $datos = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.eliminarPagos" => "required",
            "data.comentario" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Prestamos", ["Eliminar"]);

        $data = Loan::where(["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]])->first();
        if($data != null){
            $data->status = 0;
            $data->save();
            $this->deleteTransaction($data->id, $datos["comentario"]);
            if($datos["eliminarPagos"] == 1)
                $this->deletePagos($data->id);


            return Response::json([
                "mensaje" => "El préstamo se ha eliminado correctamente",
                "data" => $data
            ]);
        }else{
            \abort(402, "El préstamo no existe");
        }
    }

    public function deletePagos($idPrestamo){
        $pagos = \App\Pay::where("idPrestamo", $idPrestamo)->get();
        $idPagos = $pagos->map(function($data){
            return $data->id;
        });

        $idPagoString = implode(", ", $idPagos);
        \DB::select("update pays set status = 0 where id in($idPagoString)");
        foreach ($idPagos as $id) {
            $this->deleteTransactionPago($id);
        }
    }

    public function deleteTransaction($idReferencia, $comentario = null){
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Préstamo"])->first();
        \App\Transaction::cancel($tipo, $idReferencia, $comentario);
    }

    public function deleteTransactionPago($idReferencia){
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Pago"])->first();
        \App\Transaction::cancel($tipo, $idReferencia);
    }
}
