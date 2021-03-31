<?php
//vendor/bin/phpunit --filter test_expenses
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // protected function setUp()
    // {
    //     /**
    //      * This disables the exception handling to display the stacktrace on the console
    //      * the same way as it shown on the browser
    //      */
    //     parent::setUp();
    //     $this->withoutExceptionHandling();
    // }

    // protected function withoutExceptionHandling()
    // {
    //     $this->app->instance(ExceptionHandler::class, new class extends Handler {
    //         public function __construct() {}

    //         public function report(\Exception $e)
    //         {
    //             // no-op
    //         }

    //         public function render($request, \Exception $e) {
    //             throw $e;
    //         }
    //     });
    // }

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

        $response->dump()->assertStatus(200);
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

    /** @test */
    public function test_expenses()
    {
        $this->withoutExceptionHandling();
        $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('expenses.store'), [
            "data" => [
                "id" => null,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "caja" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
                "concepto" => "hey",
                "monto" => 100,
                "comentario" => null,
                "fecha" => "2021-03-20",
            ]
        ]);

        $response->assertStatus(200, "Response is: culo" );
        // $response->assertSessionHasErrors('email');
    }

    // public function test_expenses_destroy()
    // {
    //     $this->withoutExceptionHandling();
    //     $tipo = \App\Type::whereRenglon("gasto")->first();
    //     $response = $this->post(route('expenses.destroy'), [
    //         "data" => [
    //             "id" => 4,
    //             "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
    //             "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
    //             "caja" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
    //             "concepto" => "hey",
    //             "monto" => 100,
    //             "balanceInicial" => 100,
    //             "comentario" => null,
    //             "fecha" => "2021-03-20",
    //         ]
    //     ]);

    //     $response->assertStatus(200);
    //     // $response->assertSessionHasErrors('email');
    // }

    /** @test */
    public function test_adjust_box()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('boxes.adjust'), [
            "data" => [
                "id" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                // "caja" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
                "concepto" => "hey",
                "monto" => 20,
                "balanceInicial" => 20,
                "comentario" => null,
                "fecha" => "2021-03-20",
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_transfer_box()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('boxes.transfer'), [
            "data" => [
                "id" => 1,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "cajaDesde" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
                "cajaHacia" => ["descripcion" => "Caja2", "id" => 2, "balance" => 100],
                "concepto" => "hey",
                "monto" => 20,
                "concepto" => null,
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

    public function test_destroy_pay()
    {
        $this->withoutExceptionHandling();
        // $tipo = \App\Type::whereRenglon("gasto")->first();
        $response = $this->post(route('pays.destroy'), [
            "data" => [
                "id" => 9,
                "usuario" => ["usuario" => "jeancc29", "id" => 1, "idEmpresa" => 1],
                // "tipo" => \App\Classes\Helper::stdClassToArray($tipo),
                "pago" => ["descripcion" => "Caja1", "id" => 1, "balance" => 100],
            ]
        ]);

        $response->assertStatus(200);
        // $response->assertSessionHasErrors('email');
    }

   
    
}
