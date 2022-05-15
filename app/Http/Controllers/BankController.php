<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requestData = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
            'data.idBanco' => '',
            'data.retornarBancos' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($requestData["apiKey"]);
        \App\Classes\Helper::validatePermissions($requestData, "Bancos", ["Ver"]);

        $id = $requestData["idBanco"] ?? null;
        $retornarBancos = $requestData["retornarBancos"] ?? false;

        $data = null;

        if($id != null) {
            $data = Bank::query()->where(["idEmpresa" => $requestData["idEmpresa"], "id" => $id])->first();
            if($data == null)
                throw new \Exception("El banco no existe");
        }

        return Response::json([
            "mensaje" => "",
            "hola" => $requestData,
            "bancos" => $retornarBancos ? Bank::where("idEmpresa", $requestData["idEmpresa"])->take(20)->get() : [],
            "data" => $data
        ]);
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
        $data = request()->validate([
            "data.usuario" => "",
            "data.id" => "",
            "data.descripcion" => "",
            "data.estado" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Bancos", ["Guardar"]);


        $banco = Bank::updateOrCreate(
            ["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]],
            [
                "descripcion" => $data["descripcion"],
                "estado" => $data["estado"],
            ]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "banco" => $banco
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Roles", ["Eliminar"]);

        $banco = Bank::where(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
        if($banco != null){
            $banco->delete();

            return Response::json([
                "mensaje" => "El banco se ha eliminado correctamente",
                "banco" => $banco
            ]);
        }else{
            \abort(402, "La caja no existe");
        }
    }
}
