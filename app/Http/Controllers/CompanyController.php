<?php

namespace App\Http\Controllers;

use App\Company;
use App\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $datos = request()->validate([
        //     'data.apiKey' => '',
        //     'data.idUsuario' => '',
        // ])["data"];

        //Validar apiKey

        //Buscar usuario para obtener la empresa y luego buscar y retornar la empresa correspondiente al usuario
        // User::whereId($datos["idUsuario"])->first();

        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Configuraciones", ["Empresa"]);

        $empresa = Company::first();
        return Response::json([
            "mensaje" => "",
            "empresa" => ($empresa == null) ? null : new \App\Http\Resources\CompanyResource(Company::where("idEmpresa", $data["idEmpresa"])->first()),
            'tipos' => \App\Type::whereRenglon("mora")->get(),
            'monedas' => \App\Coin::all(),
            'nacionalidades' => Nationality::all(),
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
            'data.idUsuario' => '',
            'data.id' => '',
            'data.foto' => '',
            'data.nombreFoto' => '',
            'data.nombre' => '',
            'data.direccion' => '',
            'data.contacto' => '',
            'data.moneda' => '',
            'data.diasGracia' => '',
            'data.porcentajeMora' => '',
            'data.tipoMora' => '',
            'data.nacionalidad' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Configuraciones", ["Empresa"]);

        $nacionalidad = $datos["nacionalidad"] != null ? $datos["nacionalidad"]["id"] : null;

        //Cliente
        $fotoPerfil = null;
        if(isset($datos["foto"]))
            $fotoPerfil = $this->guardarFoto($datos["foto"], $datos["nombre"]);
        else
            $fotoPerfil = $datos["nombreFoto"];

        $contacto = \App\Contact::updateOrCreate(
            ["id" => $datos["contacto"]["id"]],
            [
                "correo" => $datos["contacto"]["correo"],
                "telefono" => $datos["contacto"]["telefono"],
                "celular" => $datos["contacto"]["celular"],
                "rnc" => $datos["contacto"]["rnc"]
            ]
            );

        $empresa = Company::updateOrCreate(
            ["id" => $datos["id"], "idEmpresa" => $datos["usuario"]["idEmpresa"]],
            [
                "foto" => $fotoPerfil,
                "nombre" => $datos["nombre"],
                "direccion" => $datos["direccion"],
                "idContacto" => $contacto->id,
                "idTipoMora" => $datos["tipoMora"]["id"],
                "idMoneda" => $datos["moneda"]["id"],
                "idNacionalidad" => $nacionalidad,
                "porcentajeMora" => $datos["porcentajeMora"],
                "diasGracia" => $datos["diasGracia"],
                "status" => 1,
            ]
        );

        return Response::json(
            [
                "mensaje" => "Se ha guardado correctamente",
                "empresa" => new \App\Http\Resources\CompanyResource($empresa)
            ]
        );
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
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }
}
