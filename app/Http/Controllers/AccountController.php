<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class AccountController extends Controller
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
        \App\Classes\Helper::validatePermissions($data, "Cuentas", ["Ver"]);

        return Response::json([
            "mensaje" => "",
            "cuentas" => \App\Http\Resources\AccountResource::collection(Account::where("idEmpresa", $data["idEmpresa"])->take(20)->get()),
            "bancos" => \App\Bank::where("idEmpresa", $data["idEmpresa"])->take(50)->get()
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
        $data = request()->validate([
            "data.usuario" => "",
            "data.id" => "",
            "data.descripcion" => "",
            "data.idBanco" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cuentas", ["Guardar"]);

        $cuenta = Account::updateOrCreate(
            ["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]],
            [
                "descripcion" => $data["descripcion"],
                "idBanco" => $data["idBanco"],
            ]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "cuenta" => new \App\Http\Resources\AccountResource($cuenta)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cuentas", ["Eliminar"]);


        $cuenta = Account::where(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
        if($cuenta != null){
            $cuenta->delete();

            return Response::json([
                "mensaje" => "La cuenta se ha eliminado correctamente",
                "cuenta" => $cuenta
            ]);
        }else{
            \abort(402, "La cuenta no existe");
        }
    }
}
