<?php
namespace App\Classes;

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

}