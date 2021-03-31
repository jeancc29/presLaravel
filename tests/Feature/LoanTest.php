<?php

namespace Tests\Feature;

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

    public function test_index_loan()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('loans.index'), [
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
}
