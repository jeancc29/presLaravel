<?php

namespace Tests\Feature;

use App\Classes\Helper;
use App\Customer;
use App\Day;
use App\Type;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanTest extends TestCase
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

    public function test_loans()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
//        $response = $this->post(route('loans.index'), [
//            "data" => [
//                "id" => 1,
//                "usuario" => "jeancc29",
//                "idEmpresa" => 1,
//                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
//                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
//                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
//            ]
//        ]);
//
//        $response->assertStatus(200);
//        // $response->assertSessionHasErrors('email');

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = $usuario->toArray();
        $data["apiKey"] = $apiKey;
        $data["idSucursal"] = null;
        $data["retornarSucursales"] = 1;

        $response = $this->post(route('loans.index'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
    }

    public function test_loan_store()
    {
        $this->withoutExceptionHandling();

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);
        $cliente = Customer::first();
        $tipoPlazo = Type::query()->where("renglon", "plazo")->first();
        $tipoAmortizacion = Type::query()->whereRenglon("amortizacion")->whereDescripcion("Cuota fija")->first();
        $diasExcluidos = Day::query()->whereWeekday(20)->get();
        $tipoDesembolso = Type::query()->where("renglon", "desembolso")->first();

        $data = array();
        $data["id"] = null;
        $data["apiKey"] = $apiKey;
        $data["usuario"] = $usuario->toArray();
        $data["usuario"]["apiKey"] = $apiKey;
        $data["cliente"] = $cliente->toArray();
        $data["tipoPlazo"] = $tipoPlazo->toArray();
        $data["tipoAmortizacion"] = $tipoAmortizacion->toArray();
        $data["monto"] = 100;
        $data["porcentajeInteres"] = 10;
        $data["porcentajeInteresAnual"] = 10;
        $data["montoInteres"] = 0;
        $data["numeroCuotas"] = 3;
        $data["fecha"] = Carbon::now();
        $data["fechaPrimerPago"] = Carbon::now();
        $data["ruta"] = null;
        $data["caja"] = null;
        $data["codigo"] = null;
        $data["diasExcluidos"] = $diasExcluidos;
        $data["porcentajeMora"] = 0;
        $data["diasGracia"] = 0;
        $data["capitalTotal"] = 100;
        $data["interesTotal"] = 10;
        $data["capitalPendiente"] = 100;
        $data["interesPendiente"] = 10;
        $data["fechaProximoPago"] = null;
        $data["cobrador"] = null;
        $data["gastoPrestamo"] = null;
        $data["garante"] = null;
        $data["garantias"] = [];
        $data["desembolso"] = [
            "id" => null,
            "tipo" => $tipoDesembolso,
            "banco" => null,
            "cuenta" => null,
            "bancoDestino" => null,
            "cuentaDestino" => null,
            "numeroCheque" => null,
            "montoBruto" => 100,
            "montoNeto" => 100
        ];

        $response = $this->post(route('loan.store'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
    }

    public function test_indexAdd_loan()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('loans.indexAdd'), [
            "data" => [
                "id" => 1,
                "usuario" => "jeancc29",
                "idEmpresa" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_testCustomFirst_loan()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('loans.testCustomFirst'), [
            "data" => [
                "id" => 1,
                "usuario" => "jeancc29",
                "idEmpresa" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_customFirst()
    {
        $this->withoutExceptionHandling();
        \App\Loan::customFirst(1);
        $this->assertTrue(true);
    }

    public function test_loan_show()
    {
        $this->withoutExceptionHandling();

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = [];
        $data["usuario"] = $usuario->toArray();
        $data["usuario"]["apiKey"] = $apiKey;
        $data["id"] = 4;

        $response = $this->post(route('loan.show'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
    }
}
