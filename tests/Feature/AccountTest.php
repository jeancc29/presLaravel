<?php
//vendor/bin/phpunit --filter test_dashboard_index
namespace Tests\Feature;

use App\Classes\Helper;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountTest extends TestCase
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

    public function test_accounts()
    {
        $this->withoutExceptionHandling();

        $usuario = User::first();
        $apiKey = Helper::jwtEncode($usuario->usuario);

        $data = $usuario->toArray();
        $data["apiKey"] = $apiKey;
        $data["retornarCuentas"] = 1;
        $data["retornarBancos"] = 1;

        $response = $this->post(route('accounts'), ["data" => $data]);
        $array = Helper::stdClassToArray($response->getData());
        $json = json_encode($array);
        echo $json;
//        foreach ($array["grupos"] as $grupo) {
//            echo $grupo["descripcion"];
//        }
//        echo "\n" . json_encode($array["ventasGrafica"]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }
}
