<?php

namespace Tests\Feature;

use App\Box;
use App\Classes\Helper;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BoxTest extends TestCase
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

    public function test_box_getBoxDataToClose()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();

        $caja = Box::query()->orderBy("id")->first();
        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = [];
        $data = $usuario->toArray();
        $data["apiKey"] = $apiKey;
        $data["id"] = 4;
        $data["idCaja"] = $caja->id;

        $response = $this->post(route('boxes.getBoxDataToClose'), [
            "data" => $data
        ]);

        $response->assertStatus(200);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;
// $response->assertSessionHasErrors('email');
    }
}
