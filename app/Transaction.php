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

        if(!isset($caja))
            return;

        $monto = (Transaction::isSum($tipo, $monto)) ? abs($monto) : \App\Classes\Helper::toNegative($monto);


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

        if($tipo["descripcion"] == "Balance inicial" || $tipo["descripcion"] == "Ajuste caja" || $tipo["descripcion"] == "Transferencia entre cajas"){
            $t = Transaction::create($arrayOfData);
            \App\Box::updateBalance($t->idCaja);
            return;
        }

        /// Si la transaccion existe pues validamos de que esta no este cerrada para poderla editar, de lo contrario pues no se
        /// podrá editar la transacccion
        if($tipo["descripcion"] != "Balance inicial" && $tipo["descripcion"] != "Ajuste caja"){
            $t = Transaction::where(["idReferencia" => $idReferencia, "idTipo" => $tipo["id"]])->first();
            if($t != null){
                if($t->status == 2){
                    abort(402, "La caja ya ha sido cerrada");
                    return;
                }
            }
        }

        // $t = Transaction::create($arrayOfData);
        if($monto < 0 && $caja["balance"] < abs($monto)){
            abort(402, "La caja no tiene monto suficiente");
            return;
        }


        $t = Transaction::updateOrCreate(
            ["idReferencia" => $idReferencia, "idTipo" => $tipo["id"]],
            $arrayOfData
        );
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

            default:
                # code...
                $isSum = ($monto > 0) ? true : false;
                break;
        }
        return $isSum;
    }

    public static function cancel($tipo, $idReferencia, $comentario = null){
        $t = Transaction::where(["idTipo" => $tipo->id, "idReferencia" => $idReferencia])->first();
        if($t == null)
            return;

        if($t->status == 2){
            abort(402, "La caja ya ha sido cerrada, asi que no se puede cancelar la transacción.");
            return;
        }

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
