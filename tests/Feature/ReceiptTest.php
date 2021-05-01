<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReceiptTest extends TestCase
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

    public function test_index_receipt()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('receipts.index'), [
            "data" => [
                "id" => null,
                'data.copia' => '',
                'data.capital' => '',
                'data.mora' => '',
                'data.interes' => '',
                'data.descuento' => '',
                'data.capitalPendiente' => '',
                'data.balancePendiente' => '',
                'data.fechaProximoPago' => '',
                'data.formaPago' => '',
                'data.firma' => '',
                "idEmpresa" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_store_receipt()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('receipts.store'), [
            "data" => [
                "id" => null,
                'copia' => 1,
                'capital' => 1,
                'mora' => 1,
                'interes' => 1,
                'descuento' => 1,
                'capitalPendiente' => 1,
                'balancePendiente' => 1,
                'fechaProximoPago' => 1,
                'formaPago' => 1,
                'firma' => 1,
                "idEmpresa" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
}
