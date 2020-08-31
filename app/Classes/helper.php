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

}