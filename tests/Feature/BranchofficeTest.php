<?php

namespace Tests\Feature;

use App\Classes\Helper;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BranchofficeTest extends TestCase
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

    public function test_branchoffices()
    {
        $this->withoutExceptionHandling();

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = $usuario->toArray();
        $data["apiKey"] = $apiKey;
        $data["idSucursal"] = null;
        $data["retornarSucursales"] = 1;

        $response = $this->post(route('branchoffices'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
    }
}
