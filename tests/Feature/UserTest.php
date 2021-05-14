<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function test_get_users()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('users.get'), [
            "data" => [
                "id" => 1,
                'copia' => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_storePerfil_users()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('users.storePerfil'), [
            "data" => [
                "id" => 2,
                'copia' => 1,
                'nombres' => "Pedro del culo",
                'apellidos' => "CUlu",
                'nombreFoto' => null,
                'contacto' => ["id" => 4, "correo" => "pedrito@no.com", "telefono" => "3211111"],
                "usuarioData" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
}
