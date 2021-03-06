<?php

namespace App\Http\Controllers;

use App\Branchoffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class BranchofficeController extends Controller
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
        \App\Classes\Helper::validatePermissions($data, "Sucursales", ["Ver"]);

        return Response::json([
            "mensaje" => "",
            // "sucursales" => \App\Http\Resources\AccountResource::collection(Account::take(20)->get()),
            "sucursales" => \App\Branchoffice::where("nombre", "!=", "Ninguna")->where("idEmpresa", $data["idEmpresa"])->take(50)->get()
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
            'data.foto' => '',
            'data.nombreFoto' => '',
            'data.nombre' => '',
            'data.direccion' => '',
            'data.ciudad' => '',
            'data.telefono1' => '',
            'data.telefono2' => '',
            'data.gerenteSucursal' => '',
            'data.gerenteCobro' => '',
            'data.status' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Sucursales", ["Guardar"]);

        //Cliente
        $fotoPerfil = null;
        if(isset($datos["foto"]))
            $fotoPerfil = $this->guardarFoto($datos["foto"], $datos["nombre"]);
        else
            $fotoPerfil = $datos["nombreFoto"];

        $sucursal = Branchoffice::updateOrCreate(
            ["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]],
            [
                "foto" => $fotoPerfil,
                "nombre" => $datos["nombre"],
                "direccion" => $datos["direccion"],
                "ciudad" => $datos["ciudad"],
                "telefono1" => $datos["telefono1"],
                "telefono2" => $datos["telefono2"],
                "gerenteSucursal" => $datos["gerenteSucursal"],
                "gerenteCobro" => $datos["gerenteCobro"],
                "status" => $datos["status"],
            ]
        );

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "sucursal" => $sucursal
        ]);
    }

    public function guardarFoto($base64Image, $documento){
        $realImage = base64_decode($base64Image);
        $safeName = $documento . time() .'.'.'png';
        $path = \App\Classes\Helper::path() . $safeName;
        $success = file_put_contents($path, $realImage);
        return $safeName;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branchoffice  $branchoffice
     * @return \Illuminate\Http\Response
     */
    public function show(Branchoffice $branchoffice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branchoffice  $branchoffice
     * @return \Illuminate\Http\Response
     */
    public function edit(Branchoffice $branchoffice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branchoffice  $branchoffice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branchoffice $branchoffice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branchoffice  $branchoffice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branchoffice $branchoffice)
    {
        $data = request()->validate([
            "data.usuario" => "required",
            "data.id" => "required",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Sucursales", ["Eliminar"]);

        $sucursal = Branchoffice::where(["id" => $data["id"], "idEmpresa" => $data["usuario"]["idEmpresa"]])->first();
        if($sucursal != null){
            $sucursal->delete();

            return Response::json([
                "mensaje" => "La sucursal se ha eliminado correctamente",
                "sucursal" => $sucursal
            ]);
        }else{
            \abort(402, "La sucursal no existe");
        }
    }
}
