<?php

use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipo = \App\Type::where(["renglon" => "mora", "descripcion" => "Capital pendiente"])->first();
        $moneda = \App\Coin::where(["codigo" => "DOP"])->first();
        $contacto = \App\Contact::updateOrCreate(
            ["correo" => "no@no.com"],
            [
                "telefono" => "8294266800",
                "rnc" => "123456",
            ]
        );
        $pais = \App\Country::whereNombre("Republica Dominicana")->first();
        $estado = \App\State::where(["nombre" => "Santiago", "idPais" => $pais->id])->first();
        $ciudad = \App\City::where(["nombre" => "Santiago", "idEstado" => $estado->id])->first();
        $direccion = \App\Address::updateOrCreate(
            ["direccion" => "Direccion de prueba"],
            [
                "idEstado" => $estado->id,
                "idCiudad" => $ciudad->id,
                "idPais" => $pais->id,
                "sector" => "Prueba"
            ]
            );

        $empresa = \App\Company::updateOrCreate(
            ["nombre" => "Prueba"],
            [
             "status" => 1,
             "idTipoMora" => $tipo->id,
             "idMoneda" => $moneda->id,
             "idContacto" => $contacto->id,
             "idEmpresa" => 1
            ]
        );
    }
}
