<?php

namespace App\Http\Controllers;

use App\Othersetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class OthersettingController extends Controller
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
        \App\Classes\Helper::validatePermissions($data, "Configuraciones", ["Otros"]);

        return Response::json([
            "data" => Othersetting::where("idEmpresa", $data["idEmpresa"])->first()
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
            'data.ocultarInteresAmortizacion' => '',
            'data.requirirSeleccionarCaja' => '',
            'data.calcularComisionACuota' => '',
            'data.mostrarCentabosRecibidos' => '',
        ])["data"];

        
        // try {
            \DB::beginTransaction();
            \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
            \App\Classes\Helper::validatePermissions($datos["usuario"], "Configuraciones", ["Otros"]);
            

            $configuracion = Othersetting::updateOrCreate(
                [
                    "id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]
                ],
                [
                    "ocultarInteresAmortizacion" => $datos["ocultarInteresAmortizacion"],
                    "requirirSeleccionarCaja" => $datos["requirirSeleccionarCaja"],
                    "calcularComisionACuota" => $datos["calcularComisionACuota"],
                    "mostrarCentabosRecibidos" => $datos["mostrarCentabosRecibidos"]
                ]
            );
            \DB::commit();

            return Response::json([
                "mensaje" => "se ha guardado correctamente",
                "data" => $configuracion
            ], 200);
        //} catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            abort(404, $th->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Othersetting  $othersetting
     * @return \Illuminate\Http\Response
     */
    public function show(Othersetting $othersetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Othersetting  $othersetting
     * @return \Illuminate\Http\Response
     */
    public function edit(Othersetting $othersetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Othersetting  $othersetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Othersetting $othersetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Othersetting  $othersetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Othersetting $othersetting)
    {
        //
    }
}
