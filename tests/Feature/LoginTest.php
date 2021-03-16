<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
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

    /** @test */
    public function box_transacciones()
    {
        $response = $this->post(route('boxes.indexTransacciones'), [
            "data" => [
                "id" => 1,
                "usuario" => "jeancc29",
                "idEmpresa" => 1
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_dashboard()
    {
        $response = $this->post(route('boxes.indexTransacciones'), [
            "data" => [
                "id" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1]
                // "idEmpresa" => 1
            ]
        ]);

        $response->assertStatus(200, "Response is: culo" );
        // $response->assertSessionHasErrors('email');
    }
}
