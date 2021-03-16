<?php
namespace App\Classes;
use Illuminate\Support\Facades\Response; 

class Helper{

    public static function path(){
        $path = public_path();
        if($contains = \Str::contains($path, '\\'))
            $path .= "\assets\perfil\\";
        else
            $path .= "/assets/perfil/";

        return $path;
    }

    public static function jwtDecode($token)
    {
        $stdClass = \Firebase\JWT\JWT::decode($token, \config('data.apiKey'), array('HS256'));
        $datos = Helper::stdClassToArray($stdClass);
        return $datos;
    }

    public static function jwtEncode($data)
    {
        $time = time();
        $key = \config('data.apiKey');

        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
            'data' => [ // información del usuario
                'data' => $data
            ]
        );

        return \Firebase\JWT\JWT::encode($token, $key);
    }

    public static function validateApiKey($apiKey){
        try {
            Helper::jwtDecode($apiKey);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                "message" => "Api key invalido"
            ], 404);
        }
    }

    public static function validatePermissions($usuario, $entidad, $permisos){
       $permisos = implode(",", $permisos);
       $idUsuario = $usuario["id"];

        $datos = \DB::select("
            select 
                pu.id
            from users u
            inner join permission_user pu on pu.idUsuario = u.id
            where u.id = $idUsuario and pu.idPermiso in (select permissions.id from permissions where permissions.idEntidad = (select entities.id from entities where entities.descripcion = '$entidad') and permissions.descripcion in ('$permisos'))
        ");

        return count($datos) > 0 ? true : Response::json(["message" => "No tiene permiso para realizar esta acción"], 404);;
    }

    public static function toNegative($number){
        return abs($number) * -1;
    }

    public static function stdClassToArray($stdClass)
    {
        return json_decode(json_encode($stdClass), true);
    }

    public static function validateMonto($caja, $monto = 0){
        // if($caja == null)
        //     return true;
        
        return 
        $caja["balance"] < abs($monto)
        ?
        abort(404, "La caja no tiene monto suficiente.")
        // Response::json([
        //     "message" => "La caja no tiene monto suficiente.",
        // ], 404)
        :
        true
        ;
    }

}