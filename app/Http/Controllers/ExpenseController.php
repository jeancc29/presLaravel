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
        return Response::json([
            'mensaje' => '',
            'tipos' => \App\Type::whereRenglon("gasto")->cursor(),
            'cajas' => \App\Box::cursor(),
            'gastos' => \App\Http\Resources\ExpenseResource::collection(Expense::cursor()),
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
            'data.id' => '',
            'data.fecha' => '',
            'data.concepto' => '',
            'data.monto' => '',
            'data.comentario' => '',
            'data.idCaja' => '',
            'data.idTipo' => '',
            'data.idUsuario' => '',
        ])["data"];


        $gasto = Expense::updateOrCreate(
            ["id" => $datos["id"]],
            [
                "id" => $datos["id"],
                "fecha" => $datos["fecha"],
                "concepto" => $datos["concepto"],
                "monto" => $datos["monto"],
                "comentario" => $datos["comentario"],
                "idCaja" => $datos["idCaja"],
                "idTipo" => $datos["idTipo"],
                "idUsuario" => $datos["idUsuario"],
            ]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "data" => new \App\Http\Resources\ExpenseResource($gasto)
        ]);
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
            'data.id' => '',
            'data.fecha' => '',
            'data.concepto' => '',
            'data.monto' => '',
            'data.comentario' => '',
            'data.idCaja' => '',
            'data.idTipo' => '',
            'data.idUsuario' => '',
        ])["data"];

        $gasto = Expense::whereId($datos["id"])->first();
        if($gasto != null){
            $gasto->delete();
        }

        return Response::json([
            "mensaje" => "Se ha eliminado correctamente",
            "gasto" => $gasto
        ]);
    }
}
