<?php

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
        return Response::json([
            "mensaje" => "",
            "cajas" => Box::where("descripcion", '!=', "Ninguna")->get(),
        ], 201);
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
            "data.id" => "",
            "data.descripcion" => "",
        ])["data"];


        $caja = Box::updateOrCreate(
            ["id" => $data["id"]],
            ["descripcion" => $data["descripcion"]]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "caja" => $caja
        ]);
    }

    public function abrirCaja(Request $request)
    {
        $data = request()->validate([
            "data.id" => "required",
            "data.balanceInicial" => "",
            "data.descripcion" => "",
        ])["data"];

        $caja = Box::whereId($data["id"])->first();
        if($caja != null){
            $caja->balanceInicial = $data["balanceInicial"];
            $caja->save();
        }else{
            return Response::json([
                "message" => "La caja no existe"
            ], 402);
        }

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
        ]);
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
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        $caja = Box::whereId($data["id"])->first();
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
