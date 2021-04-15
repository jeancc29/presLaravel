<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GuaranteeTest extends TestCase
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

    // public function test_destroy_pay()
    // {
    //     $this->withoutExceptionHandling();
    //     // $tipo = \App\Type::whereRenglon("gasto")->first();
    //     $response = $this->post(route('pays.destroy'), [
    //         "data" => [
    //             "id" => 9,
    //             "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
    //             // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
    //             "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
    //         ]
    //     ]);

    //     $response->assertStatus(200);
    //     // $response->assertSessionHasErrors('email');
    // }

    public function test_index_guarantees()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('guarantees.index'), [
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

}
