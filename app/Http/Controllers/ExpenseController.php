<?php

namespace App\Http\Controllers;

use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class ExpenseController extends Controller
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
        \App\Classes\Helper::validatePermissions($data, "Gastos", ["Ver"]);
        
        return Response::json([
            'mensaje' => '',
            'tipos' => \App\Type::whereRenglon("gasto")->cursor(),
            'cajas' => \App\Box::where("idEmpresa", $data["idEmpresa"])->cursor(),
            'gastos' => \App\Http\Resources\ExpenseResource::collection(Expense::where("idEmpresa", $data["idEmpresa"])->cursor()),
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
            'data.fecha' => '',
            'data.concepto' => '',
            'data.monto' => '',
            'data.comentario' => '',
            'data.idCaja' => '',
            'data.caja' => '',
            'data.tipo' => '',
            'data.idUsuario' => '',
        ])["data"];


        // \DB::transaction(function() use($datos){
        //     // \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            
            
        // });

        \DB::beginTransaction();
        try {
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Rutas", ["Guardar"]);
            if(isset($datos["caja"]))
                \App\Box::validateMonto($datos["caja"], $datos["monto"]);

            $gasto = Expense::updateOrCreate(
                ["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]],
                [
                    "fecha" => $datos["fecha"],
                    "concepto" => $datos["concepto"],
                    "monto" => $datos["monto"],
                    "comentario" => $datos["comentario"],
                    "idCaja" => isset($datos["caja"]) ? $datos["caja"]["id"] : null,
                    "idTipo" => $datos["tipo"]["id"],
                    "idUsuario" => $datos["usuario"]["id"],
                    "idEmpresa" => $datos["usuario"]["idEmpresa"],
                ]
            );


            if(isset($datos["caja"])){
                $tipo = \App\Classes\Helper::stdClassToArray(\App\Type::where(["descripcion" => "Gasto", "renglon" => "transaccion"])->first());
                \App\Transaction::make($datos["usuario"], $datos["caja"], $gasto->monto, $tipo, $gasto->id, $datos["tipo"]["descripcion"]);
            }

            \DB::commit();

            return Response::json([
                "mensaje" => "Se ha guardado correctamente",
                "data" => new \App\Http\Resources\ExpenseResource($gasto)
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            abort(402, "Error: {$th->getMessage()}");
        }

        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
            'data.fecha' => '',
            'data.concepto' => '',
            'data.monto' => '',
            'data.comentario' => '',
            'data.idCaja' => '',
            'data.idTipo' => '',
            'data.idUsuario' => '',
        ])["data"];

        \DB::transaction(function() use($datos){
            \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Rutas", ["Guardar"]);
    
            $gasto = Expense::whereId([$datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]])->first();
            if($gasto != null){
                $this->deleteTransaction($gasto->id);
                $gasto->delete();
            }
    
            return Response::json([
                "mensaje" => "Se ha eliminado correctamente",
                "gasto" => $gasto
            ]);
        });

        
    }

    public function deleteTransaction($idReferencia, $comentario = null){
        $tipo = \App\Type::where(["renglon" => "transaccion", "descripcion" => "Gasto"])->first();
        \App\Transaction::cancel($tipo, $idReferencia, $comentario);
    }
}
