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
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Prestamos", ["Ver"]);

        $idEmpresa = $data["idEmpresa"];

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
        $prestamos = Loan::customAll($idEmpresa);

        return Response::json([
            "prestamos" => $prestamos,
            "tipos" => \App\Type::whereIn("renglon", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->cursor(),
            "cajas" => \App\Box::where("idEmpresa", $idEmpresa)->cursor(),
            "bancos" => \App\Bank::where("idEmpresa", $idEmpresa)->cursor(),
            "cuentas" => \App\Account::where("idEmpresa", $idEmpresa)->get(),
            "dias" => \App\Day::get(),
            "configuracionPrestamo" => \App\Loansetting::where("idEmpresa", $idEmpresa)->first()
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
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Prestamos", ["Guardar"]);
        \App\Box::validateMonto($datos["caja"], $datos["monto"]);

        // return Response::json([
        //     "message" => "La caja no tiene monto suficiente. {$datos["caja"]["balance"]}",
        // ], 404);

        $prestamo = null;

        \DB::transaction(function() use($datos){
            

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
                    "capitalPendiente" => $datos["capitalPendiente"],
                    "interesPendiente" => $datos["interesPendiente"],
                    "fechaProximoPago" => $datos["fechaProximoPago"],
                    // "idUsuario" => $datos["usuario"]["id"],
                    "idEmpresa" => $datos["usuario"]["idEmpresa"],
                    "idUsuario" => $datos["usuario"]["id"],
                    "idCliente" => $datos["cliente"]["id"],
                    "idTipoPlazo" => $datos["tipoPlazo"]["id"],
                    "idTipoAmortizacion" => $datos["tipoAmortizacion"]["id"],
                    "idCaja" => ($datos["caja"] != null) ? $datos["caja"]["id"] : null,
                    // "idCobrador" => $datos["cobrador"]["id"],
                    "idCobrador" => 1,
                    // "idGasto" => $gasto->id,
                    "idDesembolso" => $desembolso->id,
                ]
                );

                if($datos["gastoPrestamo"] != null){
                    $gasto = \App\Loanexpense::updateOrCreate(
                        ["id" => $datos["gastoPrestamo"]["id"]],
                        [
                            "idTipo" => $prestamo->id, 
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
                            "numeroIdenticacion" => $datos["garante"]["numeroIdenticacion"],
                            "direccion" => $datos["garante"]["direccion"],
                            "idPrestamo" => $prestamo->id,
                        ]
                    );
                }

                \App\Amortization::where("id", ">", 0)->where("idPrestamo", $prestamo->id)->delete();
                foreach ($datos["amortizaciones"] as $amortizacion) {
                    \App\Amortization::updateOrCreate(
                        ["id" => $amortizacion["id"]],
                        [
                            "idPrestamo" => $prestamo->id,
                            "idTipo" => $amortizacion["tipo"]["id"],
                            "numeroCuota" => $amortizacion["numeroCuota"],
                            "cuota" => $amortizacion["cuota"],
                            "interes" => $amortizacion["interes"],
                            "capital" => $amortizacion["capital"],
                            "capitalRestante" => $amortizacion["capitalRestante"],
                            "capitalSaldado" => $amortizacion["capitalSaldado"],
                            "interesSaldado" => $amortizacion["interesSaldado"],
                            "fecha" => $amortizacion["fecha"],
                        ]
                    );
                }

                foreach ($datos["garantias"] as $garantia) {
                    \App\Guarantee::updateOrCreate(
                        ["id" => $garantia["id"]],
                        [
                            "tasacion" => $garantia["tasacion"],
                            "descripcion" => $garantia["descripcion"],
                            "marca" => $garantia["marca"],
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
                            "idTipoCondicion" => $garantia["condicion"]["id"],
                            "idTipo" => $garantia["tipo"]["id"],
                        ]
                        );
                }

                $tipo = \App\Classes\Helper::stdClassToArray(\App\Type::where(["descripcion" => "Préstamo", "renglon" => "transaccion"])->first());
                \App\Transaction::make($datos["usuario"], $datos["caja"], $prestamo->monto, $tipo, $prestamo->id, "Desembolso de Préstamo");
        });
        $lastPrestamo = Loan::latest('id')->where("idEmpresa", $datos["usuario"]["idEmpresa"])->first();
        // $prestamo = \DB::select("select
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
        //  where l.id = {$lastPrestamo->id}
        //  limit 1 ");
        $prestamo = Loan::customFirst($lastPrestamo->id);
        
        
        return Response::json([
            "mensaje" => "se ha guardado correctamente",
            "message" => $datos["usuario"]["idEmpresa"], 
            // "nalga" => "{$lastPrestamo->id}",
            // "datos" => $datos,
            "prestamo" => $prestamo
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
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
        ])["data"];

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

        $prestamo = Loan::customFirstAmortizaciones($datos['id']);


        $usuario = \App\User::whereId($datos["usuario"]["id"])->first();
        $cajas = $usuario->cajas;
        if(count($cajas) == 0)
            $cajas = \App\Box::where("idEmpresa", $usuario->idEmpresa)->get();

         return Response::json([
            "prestamo" => $prestamo,
            "tipos" => \App\Type::where("renglon", "desembolso")->cursor(),
            "cajas" => $cajas,
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
