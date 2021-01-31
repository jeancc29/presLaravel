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
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Rutas", ["Ver"]);

        return Response::json([
            'mensaje' => '',
            'rutas' => \App\Route::where("idEmpresa", $data["idEmpresa"])->cursor(),
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
            'data.descripcion' => 'required',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Rutas", ["Guardar"]);
        

        $ruta = Route::updateOrCreate(
            ["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]],
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
            'data.usuario' => '',
            'data.id' => '',
            'data.descripcion' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Rutas", ["Eliminar"]);
        

        try {
            $ruta = Route::whereId(["id" => $datos['id'], "idEmpresa" => $datos["usuario"]["idEmpresa"]])->first();
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
