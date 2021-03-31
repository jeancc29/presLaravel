<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
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

    public function test_index_customer()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('customers.index'), [
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

    public function test_indexAdd_customer()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('customers.indexAdd'), [
            "data" => [
                "id" => 1,
                "usuario" => "jeancc29",
                "idEmpresa" => 1, 
                "idCliente" => 1, 
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_store_customer()
    {
        $this->withoutExceptionHandling();
        $estado = \App\State::first();
        $ciudad = \App\City::first();
        $pais = \App\Country::first();
        $tipo = \App\Type::whereRenglon("situacionLaboral")->first();
        $nacionalidad = \App\Nationality::first();
        $response = $this->post(route('customers.store'), [
            "data" => [
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                "id" => null,
                "foto" => null,
                "nombres" => "Palomo",
                "apellidos" => "Elciente",
                "apodo" => "culo",
                "fechaNacimiento" => "1994-08-29",
                "numeroDependientes" => 1,
                "sexo" => "Masculino",
                "estadoCivil" => "Soltero",
                "nacionalidad" => "Culo",
                "tipoVivienda" => "Culo",
                "tiempoEnVivienda" => "Culo",
                "referidoPor" => "Culo",
                "documento" => ["id" => 1, "descripcion" => "CUlo", "tipo" => ["id" => 1, "descripcion" => "culo"]],
                "direccion" => ["id" => null, "idEstado" => $estado->id, "idCiudad" => $ciudad->id, "idPais" => $pais->id, "sector" => "culo", "numero" => 1, "direccion" => "culo"],
                "contacto" => ["id" => null, "telefono" => "999", "celular" => "829", "correo" => "no@no.com", "facebook" => null, "instagram" => null, "extension" => "123", "fax" => "123"],
                "trabajo" => null,
                "negocio" => null,
                "referencias" => [],
                "tipoSituacionLaboral" => ["id" => $tipo->id, "descripcion" => $tipo->descripcion],
                "nacionalidad" => ["id" => $nacionalidad->id, "descripcion" => $nacionalidad->descripcion],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_destroy_customer()
    {
        $this->withoutExceptionHandling();
        
        $response = $this->post(route('customers.destroy'), [
            "data" => [
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                "id" => 2,
                "idEmpresa" => 1,
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_customFirst_customer()
    {
        $this->withoutExceptionHandling();
        \App\Customer::customFirst(1);
        // $this->assertStatus(200);
        $this->assertTrue(true);
    }

    public function test_customAll_customer()
    {
        $this->withoutExceptionHandling();
        $data = \App\Customer::customAll(1);
        // $this->assertStatus(200);
        $this->assertTrue(true);
        $this->assertTrue(count($data) > 0);
    }
}
