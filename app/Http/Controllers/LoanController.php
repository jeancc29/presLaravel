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
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        $prestamos = \DB::select("select
        l.id,
        (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
        l.monto,
        l.porcentajeInteres,
        (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        l.numeroCuotas,
        l.monto as balancePendiente,
        l.monto as capitalPendiente,
        l.created_at fechaProximoPago,
        (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        l.codigo codigo,
        (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja
        from loans l 
         inner join customers c on c.id = l.idCliente 
         inner join types t on t.id = l.idTipoAmortizacion 
         inner join boxes b on b.id = l.idCaja 
         limit 50 ");
        //  where l.created_at between '{$fechaInicial}' and '{$fechaFinal}' limit 50 ");

        return Response::json([
            "prestamos" => $prestamos,
            "tipos" => \App\Type::whereIn("renglon", ["plazo", "amortizacion", "gastoPrestamo", "desembolso", "garantia", "condicionGarantia", "tipoVehiculo"])->cursor(),
            "cajas" => \App\Box::cursor(),
            "bancos" => \App\Bank::cursor(),
            "cuentas" => \App\Account::get(),
            "dias" => \App\Day::get(),
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
            'data.id' => '',
            'data.cliente' => '',
            'data.tipoPlazo' => '',
            'data.tipoAmortizacion' => '',
            'data.monto' => '',
            'data.porcentajeInteres' => '',
            'data.porcentajeInteresAnual' => '',
            'data.numeroCuotas' => '',
            'data.fecha' => '',
            'data.fechaPrimerPago' => '',
            'data.caja' => '',
            'data.codigo' => '',
            'data.diasExcluidos' => '',
            'data.porcentajeMora' => '',
            'data.diasGracia' => '',
            'data.cobrador' => '',
            'data.gastoPrestamo' => '',
            'data.garante' => '',
            'data.garantias' => '',
            'data.desembolso' => '',
            'data.usuario' => '',
            'data.amortizaciones' => '',
        ])["data"];

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
                    "numeroCuotas" => $datos["numeroCuotas"],
                    "fecha" => $datos["fecha"],
                    "fechaPrimerPago" => $datos["fechaPrimerPago"],
                    "codigo" => $datos["codigo"],
                    "porcentajeMora" => $datos["porcentajeMora"],
                    "diasGracia" => $datos["diasGracia"],
                    // "idUsuario" => $datos["usuario"]["id"],
                    "idUsuario" => 1,
                    "idCliente" => $datos["cliente"]["id"],
                    "idTipoPlazo" => $datos["tipoPlazo"]["id"],
                    "idTipoAmortizacion" => $datos["tipoAmortizacion"]["id"],
                    "idCaja" => $datos["caja"]["id"],
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

                
    
        });
        $prestamo = Loan::latest('id')->first();
        $prestamo = \DB::select("select
        l.id,
        (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto)) as cliente,
        l.monto,
        l.porcentajeInteres,
        (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        l.numeroCuotas,
        l.monto as balancePendiente,
        l.monto as capitalPendiente,
        l.created_at fechaProximoPago,
        (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        l.codigo codigo,
        (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja
        from loans l 
         inner join customers c on c.id = l.idCliente 
         inner join types t on t.id = l.idTipoAmortizacion 
         inner join boxes b on b.id = l.idCaja
         where l.id = {$prestamo->id}
         limit 1 ");
        
        
        return Response::json([
            "mensaje" => "se ha guardado correctamente",
            // "datos" => $datos,
            "prestamo" => (count($prestamo) > 0) ? $prestamo[0] : null
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
            'data.id' => '',
        ])["data"];


        $prestamo = \DB::select("select
        l.id,
        (select JSON_OBJECT('id', c.id, 'nombres', c.nombres, 'apellidos', c.apellidos, 'nombreFoto', c.foto, 'documento', (SELECT JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)), 'contacto', (SELECT JSON_OBJECT('id', co.id, 'celular', co.celular, 'correo', co.correo)))) as cliente,
        l.monto,
        l.porcentajeInteres,
        (select cuota from amortizations where amortizations.idPrestamo = l.id limit 1) as cuota,
        l.numeroCuotas,
        l.monto as balancePendiente,
        l.monto as capitalPendiente,
        l.created_at fechaProximoPago,
        (select JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) from types where types.id = l.idTipoAmortizacion) as tipoAmortizacion,
        l.codigo codigo,
        (select JSON_OBJECT('id', b.id, 'descripcion', b.descripcion)) as caja,
        (select JSON_ARRAYAGG(JSON_OBJECT('id', amortizations.id, 'capital', amortizations.capital, 'interes', amortizations.interes, 'cuota', amortizations.cuota, 'fecha', amortizations.fecha)) from amortizations where amortizations.idPrestamo = l.id) as amortizaciones
        from loans l 
         inner join customers c on c.id = l.idCliente 
         inner join types t on t.id = l.idTipoAmortizacion 
         inner join boxes b on b.id = l.idCaja
         left join documents d on d.id = c.idDocumento
         left join contacts co on co.id = c.idContacto

         where l.id = {$datos['id']}
         limit 1 ");

         return Response::json([
             "prestamo" => (count($prestamo) > 0) ? $prestamo[0] : null
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
        //
    }
}
