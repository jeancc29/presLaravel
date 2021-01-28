<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 
use Illuminate\Support\Facades\Crypt; 


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            "roles" => \App\Http\Resources\RoleResource::collection(\App\Role::all()),
            "cajas" => \App\Box::all(),
            "sucursales" => \App\Branchoffice::all(),
            "usuarios" => \App\Http\Resources\UserResource::collection(User::take(50)->get()),
            "entidades" => \App\Http\Resources\EntityResource::collection(\App\Entity::take(50)->get())
        ]);
    }

    public function login(Request $request){
        $data = request()->validate([
            'data.usuario' => "",
            "data.password" => "",
        ])["data"];

        $usuario = User::whereUsuario($data["usuario"])->whereStatus(1)->first();
        if($usuario == null)
            return Response::json([
                "message" => "Usuario no existe"
            ], 404);

        if(Crypt::decryptString($usuario->password) != $data['password'])
            return Response::json([
                "message" => "Contraseña incorrecta"
            ], 404);

        

        return Response::json([
            "mensaje" => "Datos correctos",
            "usuario" => new \App\Http\Resources\UserResource($usuario),
            "apiKey" => \App\Classes\Helper::jwtEncode($usuario->usuario)
        ]);
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
            'data.foto' => '',
            'data.usuario' => '',
            'data.nombreFoto' => '',
            "data.nombres" => "",
            "data.apellidos" => "",
            "data.password" => "",
            "data.contacto" => "",
            "data.rol" => "",
            "data.sucursal" => "",
            "data.empresa" => "",
            "data.cajas" => "",
            "data.permisos" => "",
            "data.status" => "",
        ])["data"];

        

        $usuario = User::whereUsuario($data["usuario"])->first();
        if($usuario != null){
            if($usuario->id != $data["id"])
                return Response::json([
                    "message" => "El usuario existe, debe registrar un usuario diferente",
                ], 404);
        }

        $fotoPerfil = null;
        if(isset($data["foto"]))
            $fotoPerfil = $this->guardarFoto($data["foto"], $data["nombres"]);
        else
            $fotoPerfil = $data["nombreFoto"];

        $contacto = \App\Contact::updateOrCreate(
            ["id" => $data["contacto"]["id"]],
            [
                "telefono" => $data["contacto"]["telefono"],
                "correo" => $data["contacto"]["correo"],
            ]
        );

        $dataArray = [
            "foto" => $fotoPerfil,
            "usuario" => $data["usuario"],
            "nombres" => $data["nombres"],
            "apellidos" => $data["apellidos"],
            "idContacto" => $contacto["id"],
            // "password" => $data["password"],
            "idRol" => $data["rol"]["id"],
            "idSucursal" => $data["sucursal"]["id"],
            "idEmpresa" => $data["empresa"]["id"],
            "status" => $data["status"],
        ];

        if($data["password"] != null)
            $dataArray["password"] = Crypt::encryptString($data["password"]);
        
        $usuario = User::updateOrCreate(
            ["id" => $data["id"]],
            $dataArray
        );

        $cajas = collect($data["cajas"])->map(function($d) use($usuario){
            return ["idCaja" => $d["id"], "idUsuario" => $usuario->id];
        });
        $usuario->cajas()->detach();
        $usuario->cajas()->attach($cajas);

        $permisos = collect($data["permisos"])->map(function($d) use($usuario){
            return ["idPermiso" => $d["id"], "idUsuario" => $usuario->id];
        });
        $usuario->permisos()->detach();
        $usuario->permisos()->attach($permisos);

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            // "data" => new \App\Http\Resources\RoleResource($data)
            "usuario" => new \App\Http\Resources\UserResource($usuario)
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $data = request()->validate([
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        $data = User::whereId($data["id"])->first();
        if($data != null){
            $data->delete();

            return Response::json([
                "mensaje" => "El usuario se ha eliminado correctamente",
                "usuario" => $data
            ]);
        }else{
            \abort(402, "El usuario no existe");
        }
    }
}
