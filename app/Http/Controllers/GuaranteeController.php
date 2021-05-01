<?php

namespace App\Http\Controllers;

use App\Guarantee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class GuaranteeController extends Controller
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
        \App\Classes\Helper::validatePermissions($datos, "Garantia", ["Ver"]);

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
            'data' => Guarantee::customAll($datos["idEmpresa"]),
        ], 200);
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
            'data.idEmpresa' => '',
            'data.estado' => '',
            'data.comentario' => '',
        ])["data"];


        // \DB::transaction(function() use($datos){
        //     // \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            
            
        // });

        \DB::beginTransaction();
        try {
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Garantias", ["Cambiar estado"]);
            

           $garantia = Guarantee::where(["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]])->first();
           if($garantia == null)
                abort(404, "La garantia no existe");

            $garantia->estado = $datos["estado"];
            $garantia->comentario = $datos["comentario"];
            $garantia->save();

            \DB::commit();

            return Response::json([
                "mensaje" => "Se ha guardado correctamente",
                "data" => Guarantee::customFirst($garantia->id)
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
     * @param  \App\Guarantee  $guarantee
     * @return \Illuminate\Http\Response
     */
    public function show(Guarantee $guarantee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Guarantee  $guarantee
     * @return \Illuminate\Http\Response
     */
    public function edit(Guarantee $guarantee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Guarantee  $guarantee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Guarantee $guarantee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Guarantee  $guarantee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Guarantee $guarantee)
    {
        //
    }
}
