<?php

namespace Tests\Feature;

use App\Amortization;
use App\Classes\Helper;
use App\Day;
use App\Loan;
use App\Type;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AmortizationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_amortization_french()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $amortizations = Amortization::amortizacionFrancesCuotaFija(100, 10, 5, $tipoPlazo);

        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"]->day . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }
    public function test_amortization_german()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $amortizations = Amortization::amortizacionAlemanODisminuirCuota(100, 10, 3, $tipoPlazo);

        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"]->toDateString() . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }
    public function test_amortization_fixed_interest()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $amortizations = Amortization::amortizacionInteresFijo(100, 10, 3, $tipoPlazo);

        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"]->toDateString() . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }
public function test_amortization_capital_at_the_end()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $amortizations = Amortization::amortizacionCapitalAlFinal(100, 10, 5, $tipoPlazo, null, Day::query()->whereDia("Viernes")->get());

        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"]->toDateString() . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }


    public function test_amortization_abono_french()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $l = Loan::query()
            ->orderBy("id", "desc")
            ->first();
        $amortizations = $l->amortizations()->wherePagada(0)->orderBy("id")->get();
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a->numeroCuota . "\t" . $a->fecha . "\t" . $a->capital . "\t" . $a->interes . "\t" . $a->capitalRestante;
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        $amortizations = Amortization::amortizacionFrancesCuotaFija($l->capitalPendiente - 40, $l->porcentajeInteres, $l->numeroCuotas - $l->numeroCuotasPagadas, $l->termType, null, null, $amortizations);
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        echo "\n\n Abonoo...";
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"] . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $index = $amortizations->search(function($a){
            return $a["capitalRestante"] == 0;
        });

//        $amortizations = $amortizations->slice(0, $ $amortizations->count())

//        foreach ($amortizations as $a){
//            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"] . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
//            $totalInteres += $a["interes"];
//            $totalCapital += $a["capital"];
//        }

//        echo "index del cero: $index length: " . $amortizations->count();

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }

    public function test_amortization_abono_german()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $l = Loan::query()
            ->orderBy("id", "desc")
            ->first();
        $amortizations = $l->amortizations()->wherePagada(0)->orderBy("id")->get();
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a->numeroCuota . "\t" . $a->fecha . "\t" . $a->capital . "\t" . $a->interes . "\t" . $a->capitalRestante;
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        $amortizations = Amortization::amortizacionAlemanODisminuirCuota($l->capitalPendiente - 40, $l->porcentajeInteres, $l->numeroCuotas - $l->numeroCuotasPagadas, $l->termType, null, null, $amortizations);
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        echo "\n\n Abonoo...";
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"] . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }

    public function test_amortization_abono_fixed_interes()
    {
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $l = Loan::query()
            ->orderBy("id", "desc")
            ->first();
        $amortizations = $l->amortizations()->wherePagada(0)->orderBy("id")->get();
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a->numeroCuota . "\t" . $a->fecha . "\t" . $a->capital . "\t" . $a->interes . "\t" . $a->capitalRestante;
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        $amortizations = Amortization::amortizacionInteresFijo($l->capitalPendiente - 40, $l->porcentajeInteres, $l->numeroCuotas - $l->numeroCuotasPagadas, $l->termType, null, null, $amortizations);
        $totalInteres = 0;
        $totalInteresMasCapital = 0;
        $totalCapital = 0;
        echo "\n\n Abonoo...";
        foreach ($amortizations as $a){
            echo "\n\nCuota: " . $a["numeroCuota"] . "\t" . $a["fecha"] . "\t" . $a["capital"] . "\t" . $a["interes"] . "\t" . $a["capitalRestante"];
            $totalInteres += $a["interes"];
            $totalCapital += $a["capital"];
        }

        $totalInteresMasCapital = $totalInteres + $totalCapital;

        echo "\n\n Total interes: $totalInteres";
        echo "\n\n Total interes + capital: $totalInteresMasCapital";

        assert(true);
    }
}
