<?php

namespace Tests\Feature;

use App\Loan;
use App\Type;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayTest extends TestCase
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

    public function test_destroy_pay()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('pays.destroy'), [
            "data" => [
                "id" => 9,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_index_pay()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('pays.index'), [
            "data" => [
                "id" => 1,
                "idEmpresa" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_pay_store_abono()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();

        $user = \App\User::first();
        $loan = Loan::find(2);
        $tipoAbono = $type = Type::whereRenglon("abonoCapital")->whereDescripcion("Disminuir plazo")->first();
        $tipoDesembolso = Type::query()->where("renglon", "desembolso")->first();

        $data = array();
        $data["id"] = null;
        $data["usuario"] = $user;
        $data["esAbonoACapital"] = 1;
        $data["tipoAbono"] = $tipoAbono;
        $data["tipoPago"] = $tipoDesembolso;
        $data["concepto"] = "Abono a capital (Disminuir plazo) prueba";
        $data["monto"] = 30;
        $data["capitalPagado"] = 30;
        $data["descuento"] = 0;
        $data["devuelta"] = 0;
        $data["comentario"] = "Prueba";
        $data["idPrestamo"] = $loan->id;
        $data["idCliente"] = $loan->idCliente;
        $data["fecha"] = Carbon::now()->toDateString();
        $data["caja"] = null;

        $response = $this->post(route('pays.store'), [
            "data" => $data
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }


}
