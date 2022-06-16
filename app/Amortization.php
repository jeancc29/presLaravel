<?php

namespace App;

use App\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Exception;
use Ramsey\Collection\Collection;

class Amortization extends Model
{
    protected $fillable = [
        "id",
        "numeroCuota",
        "idTipo",
        "idPrestamo",
        "cuota",
        "interes",
        "capital",
        "capitalRestante",
        "capitalSaldado",
        "interesSaldado",
        "fecha",
        "capitalPendiente",
        "interesPendiente",
        "moraPendiente",
        "pagada",
    ];


    public static function updatePendientes($idPrestamo, Paydetail $detalle, $delete = false){
        $a = Amortization::query()->where(["id" => $detalle->idAmortizacion, "idPrestamo" => $idPrestamo])->first();

//        throw new Exception("Error... idDetalle: {$detalle->id} capital: {$detalle->capital} interes: {$detalle->interes} mora: {$detalle->mora}");
        if($delete == false){
            if($detalle->capital > 0)
                $a->capitalPendiente -= $detalle->capital;

//            //Si el interes pagado == 0 eso quiere decir que el descuento establecido pagó el total del interes
//            if($detalle->interes == 0)
//                $a->interesPendiente = 0;
//            elseif($detalle->interes > 0)
//                $a->interesPendiente -= $detalle->interes;
//            //Si la mora pagado == 0 eso quiere decir que el descuento establecido pagó el total de la mora
//            if($detalle->mora == 0)
//                $a->moraPendiente = 0;
//            elseif($detalle->mora > 0)
//                $a->moraPendiente -= $detalle->mora;
            if($detalle->interes > 0)
                $a->interesPendiente -= $detalle->interes;
            if($detalle->mora > 0)
                $a->moraPendiente -= $detalle->mora;
        }else{
            $a->capitalPendiente += $detalle->capital;
            $a->interesPendiente += $detalle->interes;
            $a->moraPendiente += $detalle->mora;
        }

//            if($a->capitalPendiente <= 0 && $a->interesPendiente <= 0 && $a->moraPendiente <= 0)
        $a->pagada = $a->capitalPendiente <= 0 && $a->interesPendiente <= 0 && $a->moraPendiente <= 0;

        $a->save();
    }

    public function calculateMora($prestamo){
        $now = new Carbon(Carbon::now()->toDateString() . " 00:00:00");
        $date = new Carbon(explode(" ", $this->fecha)[0] . " 00:00:00");

//        ;

        if($prestamo->diasGracia > 0)
            $date = $date->addDays($prestamo->diasGracia);
        //Si la diferencia $diff es un numero > 0 eso quiere decir que hay dias atrasados, de lo contrario no.
        $diasAtrasados = $date->diffInDays($now, false);

        if($diasAtrasados < 0)
            return;

        if($prestamo->porcentajeMora == 0 || $prestamo->porcentajeMora == null)
            return;

        $tipoMora = Type::query()->find(Company::query()->find($prestamo->idEmpresa)->idTipoMora);

        if($tipoMora == null)
            return;

        $porcentajeMora = round($prestamo->porcentajeMora / 100, 2);

        // Retornamos la mora de acuerdo al tipo de mora de la empresa, si es Capital pendiente, Cuota vencida o Capital vencido
        $mora = 0;
        if($tipoMora->descripcion == "Capital pendiente"){
            $mora = $prestamo->capitalPendiente * $porcentajeMora;
        }
        elseif ($tipoMora->descripcion == "Cuota vencida"){
            //Multiplicamos el porcentajeMora por la cuota y esto nos dara la mora de la cuota vencida
            $mora = ($this->capitalPendiente + $this->interesPendiente) * $porcentajeMora;
        }
        else{
            $mora = $this->capitalPendiente * $porcentajeMora;
        }

        if($this->pagada != true){
            $this->mora = $mora;
            $this->moraPendiente = $mora;
            $this->save();
        }

        $prestamo->mora = $mora;
        $prestamo->save();
    }

    public static function amortizacionFrancesCuotaFija(float $monto, float $interes, int $numeroCuota, Type $tipoPlazo, Type $tipoAmortizacion, Carbon $fechaPrimerPago = null) : Collection{
        $fechaPrimerPago = $fechaPrimerPago ?? Carbon::now();
        $i = Amortization::convertirInteres($tipoPlazo, $interes);
        $cuotaCalculadaAPagar = $monto * (($i * (pow(1 + $i, $numeroCuota))) / ((pow(1 + $i, $numeroCuota)) - 1));
        $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);

        $collection = collect();
    }

    private static function convertirInteres(Type $tipoPlazo, float $interes, bool $convertirInteresDelPlazoAInteresAnual = false){
        $interesARetornar = 0;

        switch ($tipoPlazo->descripcion){
            case "Diario":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 365 : $interes / 365;
                break;
            case "Semanal":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 54 : $interes / 54;
                break;
            case "Bisemanal":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 27 : $interes / 27;
                break;
            case "Quincenal":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 24 : $interes / 24;
                break;
            case "15 y fin de mes":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 24 : $interes / 24;
                break;
            case "Mensual":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 12 : $interes / 12;
                break;
            case "Anual":
                $interesARetornar = $interes;
                break;
            case "Trimestral":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 4 : $interes / 4;
                break;
            case "Semestral":
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 2 : $interes / 2;
                break;
            default:
                $interesARetornar = $convertirInteresDelPlazoAInteresAnual ? $interes * 12 : $interes / 12;
                break;
        }
        return $interesARetornar;
    }
}
