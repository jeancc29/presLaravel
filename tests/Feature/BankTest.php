<?php

namespace Tests\Feature;

use App\Classes\Helper;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BankTest extends TestCase
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

    public function test_banks()
    {
        $this->withoutExceptionHandling();

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = $usuario->toArray();
        $data["apiKey"] = $apiKey;
        $data["retornarBancos"] = 1;

        $response = $this->post(route('banks'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;

        $response->assertStatus(200);
    }
}
