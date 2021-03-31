<?php
// command to execute unit test
// vendor/bin/phpunit --filter test_adjust_box
namespace App\Http\Controllers;

use App\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class BoxController extends Controller
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
            'data.idCaja' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos, "Cajas", ["Ver"]);

        return Response::json([
            "mensaje" => "",
            "cajas" => Box::where("descripcion", '!=', "Ninguna")->where("idEmpresa", $datos["idEmpresa"])->get(),
            "caja" => (isset($datos["idCaja"])) ? Box::customFirst($datos["idCaja"]) : null
        ], 201);
    }

    public function indexTransacciones()
    {
        $datos = request()->validate([
            'data.id' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        // \App\Classes\Helper::validateApiKey($datos["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos, "Cajas", ["Ver cierres"]);

        $usuario = \App\User::whereId($datos["id"])->first();
        $cajas = $usuario->cajas;

        //Si el usuario tiene cajas pues obtenemos nuevamente esas cajas pero con sus respectivos transacciones
        //de lo contrario pues buscamos todas las cajas de la empresa y las retornamos todas pero solo la primera caja
        //va a tener sus transacciones
        if(count($cajas) > 0){
            $idCajas = $cajas->map(function($d){
                $d->id;
            });
            $cajas = Box::customAll($idCajas);
        }else{
            $cajas = Box::where("descripcion", '!=', "Ninguna")->where("idEmpresa", $datos["idEmpresa"])->get();
            if(count($cajas) > 0){
                $cajas[0]->transacciones = Box::transacciones($cajas[0]->id);
                $cajas[0]->cierres = Box::cierres($cajas[0]->id);
            }
        }
        

        return Response::json([
            "mensaje" => "",
            "cajas" => $cajas
        ], 201);
    }

    public function transacciones()
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.idCaja' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Cajas", ["Ver cierres"]);

        return Response::json([
            "mensaje" => "",
            "transacciones" => Box::transacciones($datos["idCaja"]),
            "cierres" => Box::cierres($datos["idCaja"])
        ], 201);
    }

    public function close(){
        $datos = request()->validate([
            'data.usuario' => '',
            'data.caja' => '',
            'data.comentario' => '',
        ])["data"];

        /// VALIDATE apiKEY AND permissions
        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Cajas", ["Realizar cierres"]);

        /// VALIDATE BOX
        $caja = Box::whereId($datos["caja"]["id"])->first();
        if($caja == null)
            abort(404, "La caja no existe");

        //VALIDATE TRANSACTIONS
        $transaccionesSinCerrar = $caja->transactions()->whereStatus(1)->get();
         if(count($transaccionesSinCerrar) == 0)
            abort(404, "La caja no tiene transacciones");   

        

        /// CREATE CLOSURE AND SAVE HIS TRANSACTIONS
        $cierre = \App\Closure::create([
            "idUsuario" => $datos["usuario"]["id"],
            "idEmpresa" => $datos["usuario"]["idEmpresa"],
            "idCaja" => $caja->id, 
            "monto" => $caja->balance, 
            "comentario" => $datos["comentario"],
            "montoCheques" => 0,
            "montoTarjetas" => 0,
            "montoTransferencias" => 0
        ]);
        $transaccionesToSave = $transaccionesSinCerrar->map(function($d) use($cierre){
            return ["idTransaccion" => $d->id, "idCierre" => $cierre->id];
        });
        $cierre->transactions()->attach($transaccionesToSave);

        ///CHANGE STATUS OF TRANSACTIONS TO CERRADA
        $caja->transactions()->whereStatus(1)->update(["status" => 2]);

        /// MAKE AUTOMATIC TRANSACTIONS Balance Inciial
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Balance inicial"])->first();
        \App\Transaction::make($datos["usuario"], $datos["caja"], $datos["caja"]["balanceInicial"], $tipo, $datos["caja"]["id"], $datos["comentario"]);
    
        //SET NEW BALANCE TO BOX
        $caja->balance = $datos["caja"]["balanceInicial"];
        $caja->save();
        $cierre->usuario = $datos["usuario"];
        $caja->fresh();

        return Response::json([
            "message" => "La caja se ha cerrado correctamente",
            "transacciones" => Box::transacciones($caja->id),
            "data" => $cierre,
            "caja" => $caja
        ]);
    }

    public function showClosure(){
        $datos = request()->validate([
            'data.usuario' => '',
            'data.cierre' => '',
        ])["data"];

        /// VALIDATE apiKEY AND permissions
        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Cajas", ["Ver cierres"]);

        return Response::json([
            "message" => "La caja se ha cerrado correctamente",
            "transacciones" => \App\Closure::transacciones($datos["cierre"]["id"]),
        ]);
    }

    public function transfer(){
        $datos = request()->validate([
            'data.usuario' => '',
            'data.cajaDesde' => '',
            'data.cajaHacia' => '',
            'data.monto' => '',
            'data.concepto' => '',
        ])["data"];

        \DB::beginTransaction();
        // try {
            /// VALIDATE apiKEY AND permissions
            \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Cajas", ["Hacer transferencias"]);

            /// VALIDATE MONTO OF CAJA
            Box::validateMonto($datos["cajaDesde"]);

            /// INIT CONCEPT VARs
            $conceptoDesde = "Transf. Hacia caja {$datos['cajaHacia']['descripcion']}.";
            $conceptoHasta = "Transf. Desde caja {$datos['cajaDesde']['descripcion']}.";

            ///Adding concat concept parameter to $conceptoDesde var
            if(isset($datos["concepto"]))
                $conceptoDesde .= " {$datos['concepto']}";

            /// MAKE AUTOMATIC TRANSACTIONS Balance Inciial
            $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Transferencia entre cajas"])->first();
            \App\Transaction::make($datos["usuario"], $datos["cajaDesde"], \App\Classes\Helper::toNegative($datos["monto"]), $tipo, $datos["cajaDesde"]["id"], $conceptoDesde);
            \App\Transaction::make($datos["usuario"], $datos["cajaHacia"], $datos["monto"], $tipo, $datos["cajaHacia"]["id"], $conceptoHasta);

            \DB::commit();

            return Response::json([
                "message" => "Se ha guardado correctamente"
            ]);
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     \DB::rollback();
        //     abort(402, $th->getMessage());
        // }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            "data.usuario" => "required",
            "data.id" => "",
            "data.descripcion" => "",
            "data.validarDesgloseEfectivo" => "",
            "data.validarDesgloseCheques" => "",
            "data.validarDesgloseTarjetas" => "",
            "data.validarDesgloseTransferencias" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cajas", ["Guardar"]);


        $caja = Box::updateOrCreate(
            ["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]],
            [
                "descripcion" => $data["descripcion"],
                "validarDesgloseEfectivo" => $data["validarDesgloseEfectivo"],
                "validarDesgloseCheques" => $data["validarDesgloseCheques"],
                "validarDesgloseTarjetas" => $data["validarDesgloseTarjetas"],
                "validarDesgloseTransferencias" => $data["validarDesgloseTransferencias"],
            ]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "caja" => $caja
        ]);
    }

    public function abrirCaja(Request $request)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.balanceInicial" => "",
            "data.descripcion" => "",
            "data.comentario" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cajas", ["Abrir"]);

        $caja = Box::whereId(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
        if($caja != null){
            $caja->balanceInicial = $data["balanceInicial"];
            $caja->save();
            $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Balance inicial"])->first();
            $tipo = \App\Classes\Helper::stdClassToArray($tipo);
            \App\Transaction::make($data["usuario"], $caja, $data["balanceInicial"], $tipo, null, $data["comentario"]);
        }else{
            return Response::json([
                "message" => "La caja no existe"
            ], 402);
        }

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
        ]);
    }

    public function adjust(Request $request)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.balanceInicial" => "",
            "data.descripcion" => "",
            "data.comentario" => "",
        ])["data"];

        \DB::beginTransaction();
        try {
            // \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($data["usuario"], "Cajas", ["Realizar ajustes"]);

            if($data["balanceInicial"] < 0){
                abort(402, "El balanceIncial debe ser mayor o igual que cero");
            }

            $caja = Box::whereId(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
            if($caja != null){
                $caja->balanceInicial = $data["balanceInicial"];
                $caja->save();
                $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Ajuste caja"])->first();
                $tipo = \App\Classes\Helper::stdClassToArray($tipo);
                //Si el balance es positivo entonces 
                $calculo = $caja->balance - $data["balanceInicial"];
                $convertirANegativoOPositivo = ($caja->balance > 0 && $caja->balance > $data["balanceInicial"]) ? \App\Classes\Helper::toNegative($calculo) : abs($calculo);
                \App\Transaction::make($data["usuario"], $caja, round($convertirANegativoOPositivo, 2), $tipo, null, $data["comentario"]);
            }else{
                abort(402, "La caja no existe");
            }

            \DB::commit();

            return Response::json([
                "message" => "Se ha guardado correctamente",
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            abort(402, $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function show(Box $box)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function edit(Box $box)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Box $box)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function destroy(Box $box)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cajas", ["Eliminar"]);

        $caja = Box::where(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
        if($caja != null){
            $caja->delete();

            return Response::json([
                "mensaje" => "La caja se ha eliminado correctamente",
                "caja" => $caja
            ]);
        }else{
            \abort(402, "La caja no existe");
        }
    }
}
