<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response; 

class Transaction extends Model
{
    protected $fillable = [
        'id', 
        'idEmpresa', 
        'idUsuario', 
        'idCaja', 
        'monto', 
        'comentario', 
        'status', 
        'idTipo', 
        'idTipoPago', 
        'idReferencia'
    ];

    public static function make($usuario, $caja, $monto, $tipo, $idReferencia, $comentario, $tipoPago = null, $caja2 = null){
        if($caja == null)
            return;

            
        $monto = (Transaction::isSum($tipo, $monto)) ? abs($monto) : \App\Classes\Helper::toNegative($monto);
        if($monto < 0 && $caja["balance"] < abs($monto))
            return Response::json([
                "message" => "La caja no tiene monto suficiente",
            ], 404);
        
        $arrayOfData = [
            "idEmpresa" => $usuario["idEmpresa"],
            "idUsuario" => $usuario["id"],
            "idCaja" => $caja["id"],
            "monto" => $monto,
            "idTipo" => $tipo["id"],
            "idReferencia" => $idReferencia,
            "comentario" => $comentario
        ];

        if($tipoPago != null)
            $arrayOfData["idTipoPago"] = $tipoPago["id"];

        $t = Transaction::create($arrayOfData);
        // $caja = \App\Box::whereId($caja["id"])->first();
        // $caja->balance += $monto;
        // $caja->save();
        \App\Box::updateBalance($t->idCaja);
    }

    static function isSum($tipo, $monto = null){
        $isSum = false;
        switch ($tipo["descripcion"]) {
            case 'Balance inicial':
                $isSum = true;
                break;
            case 'Pago':
                $isSum = true;
                break;
            case 'Préstamo':
                $isSum = false;
                break;
            case 'Cancelación préstamo':
                $isSum = true;
                break;
            case 'Ajuste capital':
                $isSum = ($monto > 0 ) ? false : true;
                break;
            case 'Gasto':
                $isSum = false;
                break;
            case 'Transferencia entre cajas':
                $isSum = false;
                break;
            
            default:
                # code...
                break;
        }
        return $isSum;
    }

    public static function cancel($tipo, $idReferencia, $comentario = null){
        $t = Transaction::where(["idTipo" => $tipo->id, "idReferencia" => $idReferencia])->first();
        if($t == null)
            return;
        
        $t->status = 0;
        if($comentario != null)
            $t->comentario = $comentario;
        $t->save();

        // $caja = \App\Box::whereId($t->idCaja)->first();
        // $monto = ($t->monto < 0) ? abs($t->monto) : \App\Classes\Helper::toNegative($t->monto); 
        // $caja->balance += $monto;

        \App\Box::updateBalance($t->idCaja);
    }
}
