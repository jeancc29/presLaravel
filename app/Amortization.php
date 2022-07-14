<?php

namespace App;

use App\Classes\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use mysql_xdevapi\Exception;

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

    public static function amortizar(float $monto, float $interes, int $numeroCuota, Type $tipoAmortizacion, Type $tipoPlazo, Carbon $fechaPrimerPago = null, Collection $listaDiasExcluidos = null, Collection $amortizationsAbonoACapitalDisminuirCuota = null){
        $amortizationCollection = collect();

        if($tipoAmortizacion->descripcion == "Cuota fija")
            $amortizationCollection = Amortization::amortizacionFrancesCuotaFija($monto, $interes, $numeroCuota, $tipoPlazo, $fechaPrimerPago, $listaDiasExcluidos, $amortizationsAbonoACapitalDisminuirCuota);
        elseif ($tipoAmortizacion->descripcion == "Disminuir cuota")
            $amortizationCollection = Amortization::amortizacionAlemanODisminuirCuota($monto, $interes, $numeroCuota, $tipoPlazo, $fechaPrimerPago, $listaDiasExcluidos, $amortizationsAbonoACapitalDisminuirCuota);
        elseif ($tipoAmortizacion->descripcion == "Interes fijo")
            $amortizationCollection = Amortization::amortizacionInteresFijo($monto, $interes, $numeroCuota, $tipoPlazo, $fechaPrimerPago, $listaDiasExcluidos, $amortizationsAbonoACapitalDisminuirCuota);
        else
            $amortizationCollection = Amortization::amortizacionCapitalAlFinal($monto, $interes, $numeroCuota, $tipoPlazo, $fechaPrimerPago, $listaDiasExcluidos, $amortizationsAbonoACapitalDisminuirCuota);

        return $amortizationCollection;
    }

    public static function amortizacionFrancesCuotaFija(float $monto, float $interes, int $numeroCuota, Type $tipoPlazo, Carbon $fechaPrimerPago = null, Collection $listaDiasExcluidos = null, Collection $amortizationsAbonoACapitalDisminuirCuota = null) : \Illuminate\Support\Collection{
        $tipoAmortizacion = Type::query()->whereRenglon("amortizacion")->whereDescripcion("Cuota fija")->first();
        $fechaPrimerPago = $fechaPrimerPago ?? Carbon::now();
        $interesAnual = Amortization::convertirInteres($tipoPlazo, $interes, true);
        $i = Amortization::convertirInteres($tipoPlazo, $interesAnual) / 100;
        if($amortizationsAbonoACapitalDisminuirCuota != null){
            $cuotaCalculadaAPagar = $amortizationsAbonoACapitalDisminuirCuota[0]->cuota;
        }
        else{
            $cuotaCalculadaAPagar = $monto * (($i * (pow(1 + $i, $numeroCuota))) / ((pow(1 + $i, $numeroCuota)) - 1));
            $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);
        }
        //Amortizations collection
        $amortizationCollection = collect();

        for ($index = 0; $index < $numeroCuota; $index++){
            $montoInteres = 0;
            $montoCapital = 0;
            $montoCuota = 0;
            $capitalRestante = 0;
            $capitalSaldado = 0;

            if ($index == 0) {
                $montoInteres = round(($monto * $i), 2);
                $montoCapital = round($cuotaCalculadaAPagar - $montoInteres, 2);
                $montoCuota = $cuotaCalculadaAPagar;
                $capitalRestante = $monto - $montoCapital;
                $capitalSaldado = $montoCapital;
                $interesSaldado = $montoInteres;

                if($amortizationsAbonoACapitalDisminuirCuota != null){
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;
                }
                else
                    $amortizationCollection->push([
                        "numeroCuota" => $index + 1,
                        "cuota" => $montoCuota,
                        "interes" => $montoInteres,
                        "capital" => $montoCapital,
                        "capitalSaldado" => $capitalSaldado,
                        "interesSaldado" => $interesSaldado,
                        "capitalRestante" => $capitalRestante,
                        "fecha" => $fechaPrimerPago,
                        "tipo" => $tipoAmortizacion
                    ]);


            }else{
                $montoInteres = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante * $i : $amortizationCollection[$index - 1]["capitalRestante"] * $i, 2);
                $montoCapital = round($cuotaCalculadaAPagar - $montoInteres, 2);
                $montoCuota = $cuotaCalculadaAPagar;
                $capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $montoCapital : $amortizationCollection[$index - 1]["capitalRestante"] - $montoCapital, 2);
                $capitalSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalSaldado + $montoCapital : $amortizationCollection[$index - 1]["capitalSaldado"] + $montoCapital, 2);
                $interesSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->interesSaldado + $montoInteres : $amortizationCollection[$index - 1]["interesSaldado"] + $montoInteres, 2);

                if (($index + 1) == $numeroCuota && $amortizationsAbonoACapitalDisminuirCuota == null) {
//                    echo "\n\nCapital restante: $capitalRestante montoCapital: $montoCapital montoInteres: $montoInteres amortizationsAbonoACapitalDisminuir: " . $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante;
                    $montoCapital = round(Amortization::sumarORestarMontoSobranteAlCapital($capitalRestante, $montoCapital), 2);
                    $montoInteres = round(Amortization::sumarORestarMontoSobranteAlInteres($capitalRestante, $montoInteres), 2);
                    $capitalRestante = 0;
                }
//                else if(($index + 1) == $numeroCuota && $amortizationsAbonoACapitalDisminuirCuota != null){
//
//                }

//                echo "\n\nDia: " . $amortizationCollection[$index - 1]["fecha"]->day;

                if($amortizationsAbonoACapitalDisminuirCuota != null){
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
                    $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;

                    if($capitalRestante > 0) {
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
                    }
                    elseif($capitalRestante < 0) {
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante;
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $amortizationsAbonoACapitalDisminuirCuota[$index]->capital, 2);
                        $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                        break;
                    }
                }
                else
                    $amortizationCollection->push([
                        "numeroCuota" => $index + 1,
                        "cuota" => $montoCuota,
                        "interes" => $montoInteres,
                        "capital" => $montoCapital,
                        "capitalSaldado" => $capitalSaldado,
                        "interesSaldado" => $interesSaldado,
                        "capitalRestante" => $capitalRestante,
    //                    "fecha" => Amortization::aumentarFecha($tipoPlazo, is_string($amortizationCollection[$index - 1]["fecha"]) ? new Carbon($amortizationCollection[$index - 1]["fecha"]) : $amortizationCollection[$index - 1]["fecha"],null, $fechaPrimerPago->day),
                        "fecha" => Amortization::aumentarFecha($tipoPlazo, new Carbon($amortizationCollection[$index - 1]["fecha"]->toDateTimeString()), $fechaPrimerPago->day, $listaDiasExcluidos),
                        "tipo" => $tipoAmortizacion
                    ]);

            }
        }

        if($amortizationsAbonoACapitalDisminuirCuota != null){
            //Debemos eliminar las cuotas afectadas por abono (Disminuir el plazo)... Para eso
            //eliminaremos todas las cuotas que estan despues del primer elemento encontrado con capitalRestante == 0

            //Buscamos el indice del primer elemento que el valor del capitalRestante == 0
            $indexPrimerElementoConCapitalRestanteIgualACero = $amortizationsAbonoACapitalDisminuirCuota->search(function($a){
                return $a["capitalRestante"] == 0;
            });

            //Al tamano de la collection le restamos $indexPrimerElementoConCapitalRestanteIgualACero + 1
            //para optener los elementos a eliminar que estan despues del primer elemento con el capitalPendiente == 0
            $elementosAEliminar = $amortizationsAbonoACapitalDisminuirCuota->count() - ($indexPrimerElementoConCapitalRestanteIgualACero + 1);
            //Convertimos la cantidad de elementos a eliminar en negativo
            $elementosAEliminarEnNegativo = $elementosAEliminar * -1;

            if($elementosAEliminar > 0)
                $amortizationsAbonoACapitalDisminuirCuota = $amortizationsAbonoACapitalDisminuirCuota->slice(0, $elementosAEliminarEnNegativo);
        }

        return $amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota : $amortizationCollection;
    }

public static function amortizacionAlemanODisminuirCuota(float $monto, float $interes, int $numeroCuota, Type $tipoPlazo, Carbon $fechaPrimerPago = null, Collection $listaDiasExcluidos = null, Collection $amortizationsAbonoACapitalDisminuirCuota = null) {
    // double i = interes / 100;
    //Capital = monto / numeroCuotas;
    //interes = capitalSaldado * interes;

    $tipoAmortizacion = Type::query()->whereRenglon("amortizacion")->whereDescripcion("Disminuir cuota")->first();
    $fechaPrimerPago = ($fechaPrimerPago == null) ? Carbon::now() : $fechaPrimerPago;
    $interesAnual = Amortization::convertirInteres($tipoPlazo, $interes, true);
    $i = Amortization::convertirInteres($tipoPlazo, $interesAnual, false) /100;
    if($amortizationsAbonoACapitalDisminuirCuota != null){
        $cuotaCalculadaAPagar = $amortizationsAbonoACapitalDisminuirCuota[0]->cuota;
    }
    else{
        $cuotaCalculadaAPagar = $monto / $numeroCuota;
        $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);
    }
//    $cuotaCalculadaAPagar = $monto / $numeroCuota;
//    $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);

    $amortizationCollection = collect();
    for ($index = 0; $index < $numeroCuota; $index++) {
        $montoInteres = 0;
       $montoCapital = 0;
       $montoCuota = 0;
       $capitalRestante = 0;
       $capitalSaldado = 0;

      if ($index == 0) {
          $montoInteres = round(($monto * $i), 2);
          $montoCapital = round($amortizationsAbonoACapitalDisminuirCuota == null ? $cuotaCalculadaAPagar : $amortizationsAbonoACapitalDisminuirCuota[0]->capital, 2);
          $montoCuota = $cuotaCalculadaAPagar + $montoInteres;
          $capitalRestante = $monto - $montoCapital;
          $capitalSaldado = $montoCapital;
          $interesSaldado = $montoInteres;

          if($amortizationsAbonoACapitalDisminuirCuota != null){
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;
          }
          else
              $amortizationCollection->push([
                  "numeroCuota" => $index + 1,
                  "cuota" => $montoCuota,
                  "interes" => $montoInteres,
                  "capital" => $montoCapital,
                  "capitalSaldado" => $capitalSaldado,
                  "interesSaldado" => $interesSaldado,
                  "capitalRestante" => $capitalRestante,
                  "fecha" => $fechaPrimerPago,
                  "tipo" => $tipoAmortizacion
              ]);
      } else {
          // print("AmortizacionService frances: ${index} length: ${lista.length}");
          // print("AmortizacionService frances: ${lista[index - 1].capitalRestante}");
          $montoInteres = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante * $i : $amortizationCollection[$index - 1]["capitalRestante"] * $i, 2);
          $montoCapital = round($amortizationsAbonoACapitalDisminuirCuota == null ? $cuotaCalculadaAPagar : $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capital, 2);
          $montoCuota = $cuotaCalculadaAPagar + $montoInteres;
          $capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $montoCapital : $amortizationCollection[$index - 1]["capitalRestante"] - $montoCapital, 2);
          $capitalSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalSaldado + $montoCapital : $amortizationCollection[$index - 1]["capitalSaldado"] + $montoCapital, 2);
          $interesSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->interesSaldado + $montoInteres : $amortizationCollection[$index - 1]["interesSaldado"] + $montoInteres, 2);

          if (($index + 1) == $numeroCuota && $amortizationsAbonoACapitalDisminuirCuota == null) {
              $montoCapital = round(Amortization::sumarORestarMontoSobranteAlCapital($capitalRestante, $montoCapital), 2);
              $montoInteres = round(Amortization::sumarORestarMontoSobranteAlInteres($capitalRestante, $montoInteres), 2);
              $capitalRestante = 0;
          }

          if($amortizationsAbonoACapitalDisminuirCuota != null){
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;

              if($capitalRestante > 0) {
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
              }
              //Si el $capitalRestante es menor que cero, entonces, hemos encontrado la cuota desde el cual se puede aplicar el abonoACapitalDiminuirPlazo
              elseif($capitalRestante < 0) {
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $amortizationsAbonoACapitalDisminuirCuota[$index]->capital, 2);
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                  break;
              }
          }
          else
              $amortizationCollection->push([
                  "numeroCuota" => $index + 1,
                  "cuota" => $montoCuota,
                  "interes" => $montoInteres,
                  "capital" => $montoCapital,
                  "capitalSaldado" => $capitalSaldado,
                  "interesSaldado" => $interesSaldado,
                  "capitalRestante" => $capitalRestante,
                  "fecha" => Amortization::aumentarFecha($tipoPlazo, new Carbon($amortizationCollection[$index - 1]["fecha"]->toDateTimeString()), $fechaPrimerPago->day, $listaDiasExcluidos),
                  "tipo" => $tipoAmortizacion
            ]);
      }
    }
    // lista.forEach((element) {print("pagado: ${element.capitalSaldado} restante: ${element.capitalRestante} cuota: ${element.cuota} capital: ${element.capital} interes: ${element.interes}");});
    // print("resultado _amortizacionFrances: $r");
    // print("resultado i _amortizacionFrances: $i - ${pow((1+i), cuota) - 1}");

    if($amortizationsAbonoACapitalDisminuirCuota != null){
        //Debemos eliminar las cuotas afectadas por abono (Disminuir el plazo)... Para eso
        //eliminaremos todas las cuotas que estan despues del primer elemento encontrado con capitalRestante == 0

        //Buscamos el indice del primer elemento que el valor del capitalRestante == 0
        $indexPrimerElementoConCapitalRestanteIgualACero = $amortizationsAbonoACapitalDisminuirCuota->search(function($a){
            return $a["capitalRestante"] == 0;
        });

        //Al tamano de la collection le restamos $indexPrimerElementoConCapitalRestanteIgualACero + 1
        //para optener los elementos a eliminar que estan despues del primer elemento con el capitalPendiente == 0
        $elementosAEliminar = $amortizationsAbonoACapitalDisminuirCuota->count() - ($indexPrimerElementoConCapitalRestanteIgualACero + 1);
        //Convertimos la cantidad de elementos a eliminar en negativo
        $elementosAEliminarEnNegativo = $elementosAEliminar * -1;

        if($elementosAEliminar > 0)
            $amortizationsAbonoACapitalDisminuirCuota = $amortizationsAbonoACapitalDisminuirCuota->slice(0, $elementosAEliminarEnNegativo);
    }

    return $amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota : $amortizationCollection;
  }

public static function amortizacionInteresFijo(float $monto, float $interes, int $numeroCuota, Type $tipoPlazo, Carbon $fechaPrimerPago = null, Collection $listaDiasExcluidos = null, Collection $amortizationsAbonoACapitalDisminuirCuota = null) {
    // double i = interes / 100;
    //Capital = monto / numeroCuotas;
    //interes = capitalSaldado * interes;
    $tipoAmortizacion = Type::query()->whereRenglon("amortizacion")->whereDescripcion("Interes fijo")->first();
    $fechaPrimerPago = ($fechaPrimerPago == null) ? Carbon::now() : $fechaPrimerPago;
    $interesAnual = Amortization::convertirInteres($tipoPlazo, $interes, true);
    $i = Amortization::convertirInteres($tipoPlazo, $interesAnual, false) / 100;
    if($amortizationsAbonoACapitalDisminuirCuota != null){
        $cuotaCalculadaAPagar = $amortizationsAbonoACapitalDisminuirCuota[0]->cuota;
    }
    else{
        $cuotaCalculadaAPagar = $monto / $numeroCuota;
        $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);
    }
//    $cuotaCalculadaAPagar = $monto / $numeroCuota;
//    $cuotaCalculadaAPagar = round($cuotaCalculadaAPagar, 2);

    $amortizationCollection = collect();
    for ($index = 0; $index < $numeroCuota; $index++) {
         $montoInteres = 0;
       $montoCapital = 0;
       $montoCuota = 0;
       $capitalRestante = 0;
       $capitalSaldado = 0;

      if ($index == 0) {
          $montoInteres = round(($monto * $i), 2);
          $montoCapital = round($amortizationsAbonoACapitalDisminuirCuota == null ? $cuotaCalculadaAPagar : $cuotaCalculadaAPagar - $montoInteres, 2);
          $montoCuota = $cuotaCalculadaAPagar + $montoInteres;
          $capitalRestante = $monto - $montoCapital;
          $capitalSaldado = $montoCapital;
          $interesSaldado = $montoInteres;

          if($amortizationsAbonoACapitalDisminuirCuota != null){
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;
          }
          else
              $amortizationCollection->push([
                  "numeroCuota" => $index + 1,
                  "cuota" => $montoCuota,
                  "interes" => $montoInteres,
                  "capital" => $montoCapital,
                  "capitalSaldado" => $capitalSaldado,
                  "interesSaldado" => $interesSaldado,
                  "capitalRestante" => $capitalRestante,
                  "fecha" => $fechaPrimerPago,
                  "tipo" => $tipoAmortizacion
            ]);
      } else {
          // print("AmortizacionService frances: ${index} length: ${lista.length}");
          // print("AmortizacionService frances: ${lista[index - 1].capitalRestante}");
          $montoInteres = round($monto * $i, 2);
          $montoCapital = round($amortizationsAbonoACapitalDisminuirCuota == null ? $cuotaCalculadaAPagar : $cuotaCalculadaAPagar - $montoInteres, 2);
          $montoCuota = $cuotaCalculadaAPagar + $montoInteres;
          $capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $montoCapital : $amortizationCollection[$index - 1]["capitalRestante"] - $montoCapital, 2);
          $capitalSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalSaldado + $montoCapital : $amortizationCollection[$index - 1]["capitalSaldado"] + $montoCapital, 2);
          $interesSaldado = round($amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->interesSaldado + $montoInteres : $amortizationCollection[$index - 1]["interesSaldado"] + $montoInteres, 2);

          if (($index + 1) == $numeroCuota && $amortizationsAbonoACapitalDisminuirCuota == null) {
              $montoCapital = round(Amortization::sumarORestarMontoSobranteAlCapital($capitalRestante, $montoCapital), 2);
              $montoInteres = round(Amortization::sumarORestarMontoSobranteAlInteres($capitalRestante, $montoInteres), 2);
              $capitalRestante = 0;
          }

          if($amortizationsAbonoACapitalDisminuirCuota != null){
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interes = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesPendiente = $montoInteres;
              $amortizationsAbonoACapitalDisminuirCuota[$index]->interesSaldado = $interesSaldado;

              if($capitalRestante > 0) {
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $montoCapital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $montoCapital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = $capitalRestante;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $capitalSaldado;
              }
              //Si el $capitalRestante es menor que cero, entonces, hemos encontrado la cuota desde el cual se puede aplicar el abonoACapitalDiminuirPlazo
              elseif($capitalRestante < 0) {
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capital = $amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalPendiente = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalRestante = round($amortizationsAbonoACapitalDisminuirCuota[$index - 1]->capitalRestante - $amortizationsAbonoACapitalDisminuirCuota[$index]->capital, 2);
                  $amortizationsAbonoACapitalDisminuirCuota[$index]->capitalSaldado = $amortizationsAbonoACapitalDisminuirCuota[$index]->capital;
                  break;
              }
          }
          else
              $amortizationCollection->push([
                  "numeroCuota" => $index + 1,
                  "cuota" => $montoCuota,
                  "interes" => $montoInteres,
                  "capital" => $montoCapital,
                  "capitalSaldado" => $capitalSaldado,
                  "interesSaldado" => $interesSaldado,
                  "capitalRestante" => $capitalRestante,
                  "fecha" => Amortization::aumentarFecha($tipoPlazo, $amortizationCollection[$index - 1]["fecha"], $fechaPrimerPago->day),
                  "tipo" => $tipoAmortizacion
              ]);
      }
    }
    // lista.forEach((element) {print("pagado: ${element.capitalSaldado} restante: ${element.capitalRestante} cuota: ${element.cuota} capital: ${element.capital} interes: ${element.interes}");});
    // print("resultado _amortizacionFrances: $r");
    // print("resultado i _amortizacionFrances: $i - ${pow((1+i), cuota) - 1}");

    if($amortizationsAbonoACapitalDisminuirCuota != null){
        //Debemos eliminar las cuotas afectadas por abono (Disminuir el plazo)... Para eso
        //eliminaremos todas las cuotas que estan despues del primer elemento encontrado con capitalRestante == 0

        //Buscamos el indice del primer elemento que el valor del capitalRestante == 0
        $indexPrimerElementoConCapitalRestanteIgualACero = $amortizationsAbonoACapitalDisminuirCuota->search(function($a){
            return $a["capitalRestante"] == 0;
        });

        //Al tamano de la collection le restamos $indexPrimerElementoConCapitalRestanteIgualACero + 1
        //para optener los elementos a eliminar que estan despues del primer elemento con el capitalPendiente == 0
        $elementosAEliminar = $amortizationsAbonoACapitalDisminuirCuota->count() - ($indexPrimerElementoConCapitalRestanteIgualACero + 1);
        //Convertimos la cantidad de elementos a eliminar en negativo
        $elementosAEliminarEnNegativo = $elementosAEliminar * -1;

        if($elementosAEliminar > 0)
            $amortizationsAbonoACapitalDisminuirCuota = $amortizationsAbonoACapitalDisminuirCuota->slice(0, $elementosAEliminarEnNegativo);
    }

    return $amortizationsAbonoACapitalDisminuirCuota != null ? $amortizationsAbonoACapitalDisminuirCuota : $amortizationCollection;
  }

public static function amortizacionCapitalAlFinal(float $monto, float $interes, int $numeroCuota, Type $tipoPlazo, Carbon $fechaPrimerPago = null, Collection $listaDiasExcluidos = null) {
    // double i = interes / 100;
    //Capital = monto / numeroCuotas;
    //interes = capitalSaldado * interes;
    $tipoAmortizacion = Type::query()->whereRenglon("amortizacion")->whereDescripcion("Capital al final")->first();
    $fechaPrimerPago = ($fechaPrimerPago == null) ? Carbon::now() : $fechaPrimerPago;
    $interesAnual = Amortization::convertirInteres($tipoPlazo, $interes, true);
    $i = Amortization::convertirInteres($tipoPlazo, $interesAnual) / 100;

    $amortizationCollection = collect();
    for ($index = 0; $index < $numeroCuota; $index++) {
         $montoInteres = 0;
       $montoCapital = 0;
       $montoCuota = 0;
       $capitalRestante = 0;
       $capitalSaldado = 0;

      if ($index == 0) {
          $montoInteres = round(($monto * $i), 2);
          $montoCapital = 0;
          $montoCuota = $montoInteres;
          $capitalRestante = $monto - $montoCapital;
          $capitalSaldado = $montoCapital;
          $interesSaldado = $montoInteres;
          $amortizationCollection->push([
              "numeroCuota" => $index + 1,
              "cuota" => $montoCuota,
              "interes" => $montoInteres,
              "capital" => $montoCapital,
              "capitalSaldado" => $capitalSaldado,
              "interesSaldado" => $interesSaldado,
              "capitalRestante" => $capitalRestante,
              "fecha" => $fechaPrimerPago,
              "tipo" => $tipoAmortizacion
          ]);
      } else if ($index + 1 < $numeroCuota && $index > 0) {
          $montoInteres = round(($monto * $i), 2);
          $montoCapital = 0;
          $montoCuota = $montoInteres;
          $capitalRestante = $monto - $montoCapital;
          $capitalSaldado = $montoCapital;
          $interesSaldado = $montoInteres;
          $amortizationCollection->push([
              "numeroCuota" => $index + 1,
              "cuota" => $montoCuota,
              "interes" => $montoInteres,
              "capital" => $montoCapital,
              "capitalSaldado" => $capitalSaldado,
              "interesSaldado" => $interesSaldado,
              "capitalRestante" => $capitalRestante,
              "fecha" => Amortization::aumentarFecha($tipoPlazo, new Carbon($amortizationCollection[$index - 1]["fecha"]), $fechaPrimerPago->day, $listaDiasExcluidos),
              "tipo" => $tipoAmortizacion
            ]);
      } else {
          // print("AmortizacionService "frances" => $${index} length: ${lista.length}");
          // print("AmortizacionService frances: ${lista[index - 1].capitalRestante}");
          $montoInteres = round($monto * $i, 2);
          $montoCapital = $monto;
          $montoCuota = $montoCapital + $montoInteres;
          $capitalRestante = round($amortizationCollection[$index - 1]["capitalRestante"] - $montoCapital, 2);
          $capitalSaldado = round($amortizationCollection[$index - 1]["capitalSaldado"] + $montoCapital, 2);
          $interesSaldado = round($amortizationCollection[$index - 1]["interesSaldado"] + $montoInteres, 2);

          if (($index + 1) == $numeroCuota) {
              $montoCapital = round(Amortization::sumarORestarMontoSobranteAlCapital($capitalRestante, $montoCapital), 2);
              $montoInteres = round(Amortization::sumarORestarMontoSobranteAlInteres($capitalRestante, $montoInteres), 2);
              $capitalRestante = 0;
          }
          $amortizationCollection->push([
              "numeroCuota" => $index + 1,
              "cuota" => $montoCuota,
              "interes" => $montoInteres,
              "capital" => $montoCapital,
              "capitalSaldado" => $capitalSaldado,
              "interesSaldado" => $interesSaldado,
              "capitalRestante" => $capitalRestante,
              "fecha" => Amortization::aumentarFecha($tipoPlazo, new Carbon($amortizationCollection[$index - 1]["fecha"]), $fechaPrimerPago->day, $listaDiasExcluidos),
              "tipo" => $tipoAmortizacion
          ]);
      }
    }
    // lista.forEach((element) {print("pagado: ${element.capitalSaldado} restante: ${element.capitalRestante} cuota: ${element.cuota} capital: ${element.capital} interes: ${element.interes}");});
    // print("resultado _amortizacionFrances: $r");
    // print("resultado i _amortizacionFrances: $i - ${pow((1+i), cuota) - 1}");
    return $amortizationCollection;
  }

    private static function sumarORestarMontoSobranteAlCapital(float $capitalRestante, float $montoCapital) {
        if ($capitalRestante > 0) {
          $montoCapital = $montoCapital + $capitalRestante;
        } else if ($capitalRestante < 0)
        $montoCapital = $montoCapital - abs($capitalRestante);

    // print("amortizationservice _sumarORestarMontoSobranteAlCapital montoCapital: $montoCapital restante: $capitalRestante");
        return $montoCapital;
    }

    private static function sumarORestarMontoSobranteAlInteres(float $capitalRestante, float $montoInteres) {
        if ($capitalRestante > 0) {
          $montoInteres = $montoInteres - $capitalRestante;
        } else if ($capitalRestante < 0)
        $montoInteres = $montoInteres + abs($capitalRestante);

    // print("amortizationservice _sumarORestarMontoSobranteAlInteres montoInteres: $montoInteres restante: $capitalRestante");
        return $montoInteres;
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

    public static function aumentarFecha(Type $tipoPlazo, Carbon $fecha, int $dayOfTheMonthToAmortize, Collection $listaDiasExcluidos = null) {
        $counter = 0;
        switch ($tipoPlazo->descripcion) {
        case "Diario":
            if ($listaDiasExcluidos == null)
                $fecha = $fecha->addDay();
            else {
                $esDiaExcluido = false;
                $fecha = $fecha->addDay();
          while ($esDiaExcluido != 1) {
              $counter++;
//              echo "\n\n\App\Amortization before search esDiaExcluido: $esDiaExcluido";
              $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
//              $esDiaExcluido = true;
//              echo "\n\n\App\Amortization after search esDiaExcluido: $esDiaExcluido";
              if ($esDiaExcluido) $fecha = $fecha->addDay();
              if($counter == 4)
                  return $fecha;
          }
        }
            break;
        case "Semanal":
            if ($listaDiasExcluidos == null)
//                $fecha = $fecha->addDay(Duration(days: 7));
                $fecha = $fecha->addDays(7);
            else {
                $esDiaExcluido = false;
                $fecha = $fecha->addDays(7);
              while ($esDiaExcluido != 1) {
                $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
                if ($esDiaExcluido) $fecha = $fecha->addDay();
              }
            }
            break;
        case "Bisemanal":
            if ($listaDiasExcluidos == null)
                $fecha = $fecha->addDays(14);
            else {
                $esDiaExcluido = false;
                $fecha = $fecha->addDays(14);
                while ($esDiaExcluido != 1) {
                    $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
//                    $esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                    if ($esDiaExcluido) $fecha = $fecha->addDay();
                }
            }
            break;
        case "Quincenal":
            if ($listaDiasExcluidos == null)
                $fecha = $fecha->addDays(15);
            else {
                $esDiaExcluido = false;
                $fecha = $fecha->addDays(15);
                while ($esDiaExcluido != 1) {
                    $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
//                    $esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                    if ($esDiaExcluido) $fecha = $fecha->addDay();
                }
            }
            break;
        case "15 y fin de mes":
            $fecha->lastOfMonth();
            if ($listaDiasExcluidos == null) {
                if ($fecha->day < 15) {
                    $diasFaltantesParaLlegarAlDia15 = 15 - $fecha->day;
                    $fecha = $fecha->addDays($diasFaltantesParaLlegarAlDia15);
                } else {
                    $fecha = $fecha->lastOfMonth();
                }
            } else {
                $esDiaExcluido = false;
                if ($fecha->day < 15) {
                  $diasFaltantesParaLlegarAlDia15 = 15 - $fecha->day;
                  $fecha = $fecha->addDays($diasFaltantesParaLlegarAlDia15);
                } else {
                  $fecha = $fecha->lastOfMonth();
                }
          while ($esDiaExcluido != 1) {
//              $fecha = $fecha->addDays(7);
              $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
//              esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
            if ($esDiaExcluido) $fecha = $fecha->addDay();
          }
        }
            break;
        case "Mensual":
            //Debo guardar el dia exacto de la amortizacion asi al momento de que
            //el dia de cada mes sea 31 y el siguiente mes sea 28 entonces el otro
            //siguiente mes debe caer 31 y no 28
            if ($listaDiasExcluidos == null) {
                // print("aumentarFecha dayOfTheMonth$dayOfTheMonthToAmortize: $dayOfTheMonthToAmortize");
                $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize);
            } else {
                  $esDiaExcluido = false;
                  $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize);
                  while ($esDiaExcluido != 1) {
                      $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
        //              esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                      if ($esDiaExcluido) $fecha = $fecha->addDay();
                  }
            }
            break;
        case "Anual":
            if ($listaDiasExcluidos == null)
//                $fecha = new DateTime(fecha.year + 1, fecha.month, fecha.day);
                $fecha = $fecha->addYearNoOverflow();
            else {
                $esDiaExcluido = false;
                $fecha = $fecha->addYearNoOverflow();
              while ($esDiaExcluido != 1) {
                  $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
    //              $esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                if ($esDiaExcluido) $fecha = $fecha->addDay();
              }
            }
            break;
        case "Trimestral":
            if ($listaDiasExcluidos == null) {
                $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize, 3);
            } else {
                $esDiaExcluido = false;
                $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize, 3);
                while ($esDiaExcluido != 1) {
                  $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
    //              esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                  if ($esDiaExcluido) $fecha = $fecha.addDay();
                }
            }
            break;
        case "Semestral":
            if ($listaDiasExcluidos == null) {
                $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize, 6);
            } else {
                $esDiaExcluido = false;
                $fecha = Helper::getNextMonth($fecha, $dayOfTheMonthToAmortize, 6);
                while ($esDiaExcluido != 1) {
                    $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
        //              esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                    if ($esDiaExcluido) $fecha = $fecha->addDay();
                }
            }
            break;
        default:
            if ($listaDiasExcluidos == null) {
                $nextMonth = Helper::getNextMonth($fecha);
                $lastDayOfMonth = $nextMonth->lastOfMonth();
                if ($fecha->day != $lastDayOfMonth->day)
                    $fecha = $lastDayOfMonth;
                else {
                    $fecha = Helper::getNextMonth($fecha);
                }
            } else {
                $esDiaExcluido = false;
                $nextMonth = Helper::getNextMonth($fecha);
                $lastDayOfMonth = $nextMonth->lastOfMonth();
              if ($fecha->day != $lastDayOfMonth->day)
                  $fecha = $lastDayOfMonth;
              else {
                  $fecha = Helper::getNextMonth($fecha);
              }
              while ($esDiaExcluido != 1) {
                  $esDiaExcluido = $listaDiasExcluidos->first(function($date) use($fecha){return $date->weekday == $fecha->dayOfWeek;}) != null;
//                  esDiaExcluido = (listaDiasExcluidos.indexWhere((element) => element.weekday == fecha.weekday) != -1);
                if ($esDiaExcluido) $fecha = $fecha->addDay();
              }
        }
    }

    return $fecha;
}

//    public static function abonoACapitalDisminuirPlazo(Loan $prestamo, float $montoAbono) : Collection{
//        $capitalPendiente = $prestamo->capitalPendiente - $montoAbono;
//
//        echo "\n\ncapitalSinAbono: " . $prestamo->capitalPendiente . " capitaConAbono: " . $capitalPendiente;
//
//        $amortizationsToReturn = collect();
//        $amortizations = Amortization::query()->where(["idPrestamo" => $prestamo->id, "pagado" => 0])->get();
//        for ($c = 0; $c < $amortizations->count(); $c++){
//            if($c == 0) {
//                $amortizations[$c]->capitalRestante = $amortizations[$c]->capitalRestante - $montoAbono;
//            }
//        }
//    }

}
