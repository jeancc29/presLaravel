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
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);
        \App\Classes\Helper::validatePermissions($data, "Roles", ["Ver"]);

        $rolProgramador = \App\Role::whereDescripcion("Programador")->first();

        return Response::json([
            "roles" => \App\Http\Resources\RoleResource::collection(\App\Role::where("idEmpresa", $data["idEmpresa"])->where("id", "!=", $rolProgramador->id)->take(50)->get()),
            "cajas" => \App\Box::where("idEmpresa", $data["idEmpresa"])->take(50)->get(),
            "sucursales" => \App\Branchoffice::where("idEmpresa", $data["idEmpresa"])->take(50)->get(),
            "usuarios" => \App\Http\Resources\UserResource::collection(User::where("idEmpresa", $data["idEmpresa"])->where("id", "!=", $data["id"])->where("idRol", "!=", $rolProgramador->id)->take(50)->get()),
            "entidades" => \App\Http\Resources\EntityResource::collection(\App\Entity::take(50)->get()),
            "rutas" => \App\Route::where("idEmpresa", $data["idEmpresa"])->get()
        ]);
    }

    public function get()
    {
        $data = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["apiKey"]);

        return Response::json([
            "data" => User::customFirst($datos["idEmpresa"], $datos["id"]),
            "roles" => \App\Http\Resources\RoleResource::collection(\App\Role::where("idEmpresa", $data["idEmpresa"])->where("id", "!=", $rolProgramador->id)->take(50)->get()),
            "cajas" => \App\Box::where("idEmpresa", $data["idEmpresa"])->take(50)->get(),
            "sucursales" => \App\Branchoffice::where("idEmpresa", $data["idEmpresa"])->take(50)->get(),
            "usuarios" => \App\Http\Resources\UserResource::collection(User::where("idEmpresa", $data["idEmpresa"])->where("id", "!=", $data["id"])->where("idRol", "!=", $rolProgramador->id)->take(50)->get()),
            "entidades" => \App\Http\Resources\EntityResource::collection(\App\Entity::take(50)->get()),
            "rutas" => \App\Route::where("idEmpresa", $data["idEmpresa"])->get()
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

            $empresa = \App\Company::whereId($usuario->idEmpresa)->first();
            $moneda = $empresa->moneda;

        return Response::json([
            "mensaje" => "Datos correctos",
            "usuario" => new \App\Http\Resources\UserResource($usuario),
            "apiKey" => \App\Classes\Helper::jwtEncode($usuario->usuario),
            "moneda" => $moneda
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
            "data.usuarioData" => "",
            "data.usuario" => "",
            "data.id" => "",
            'data.foto' => '',
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
            "data.ruta" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuarioData"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuarioData"], "Usuarios", ["Guardar"]);

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
            "idSucursal" => isset($data["sucursal"]) ? $data["sucursal"]["id"] : null,
            "idEmpresa" => $data["usuarioData"]["id"],
            "status" => $data["status"],
            "idRuta" => isset($data["ruta"]) ? $data["ruta"]["id"] : null
        ];

        if($data["password"] != null)
            $dataArray["password"] = Crypt::encryptString($data["password"]);

            // return Response::json([
            //     "message" => "Yo",
            //     "data" => $dataArray
            // ], 404);
        
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
            "data.usuario" => "required",
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($data["usuario"], "Cajas", ["Guardar"]);

        $data = User::whereId($data["id"])->first();
        if($data != null){
            $data->permisos()->detach();
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
