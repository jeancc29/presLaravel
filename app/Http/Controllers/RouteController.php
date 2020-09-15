<?php

namespace App\Http\Controllers;

use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class RouteController extends Controller
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
            'rutas' => \App\Route::cursor(),
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
            'data.descripcion' => 'required',
        ])["data"];

        $ruta = Route::updateOrCreate(
            ["id" => $datos["id"]],
            [
                "descripcion" => $datos["descripcion"],
            ]
        );
        return Response::json([
            "ruta" => $ruta,
            "mensaje" => "Se ha guardado correctamente",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function edit(Route $route)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Route $route)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function destroy(Route $route)
    {
        $datos = request()->validate([
            'data.id' => '',
            'data.descripcion' => '',
        ])["data"];

        try {
            $ruta = Route::whereId($datos['id'])->first();
            if($ruta != null)
            {
                $ruta->delete();
                return Response::json([
                    "mensaje" => "Se ha eliminado correctamente",
                    "ruta" => $ruta
                ]);
            }else{
                return Response::json([
                    "mensaje" => "Ruta no existe",
                    "errores" => 1,
                    "ruta" => $datos
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            \abort(400, "Error: " . $th);
        }

    }
}
