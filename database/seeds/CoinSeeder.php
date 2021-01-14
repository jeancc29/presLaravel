<?php

use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Coin::updateOrCreate(
            ["codigo" => "USD"],
            [
                "nombre" => "Dólar estadounidense",
                "simbolo" => "$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "CAD"],
            [
                "nombre" => "Dólar Canadiense",
                "simbolo" => "CA$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "EUR"],
            [
                "nombre" => "Euro",
                "simbolo" => "€",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "ARS"],
            [
                "nombre" => "Peso Argentino",
                "simbolo" => "AR$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "VES"],
            [
                "nombre" => "Bolívar Venezolano",
                "simbolo" => "Bs.",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "CLP"],
            [
                "nombre" => "Peso Chileno",
                "simbolo" => "CL$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "COP"],
            [
                "nombre" => "Peso Colombiano",
                "simbolo" => "CO$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "CRC"],
            [
                "nombre" => "Colón Costarricense",
                "simbolo" => "₡",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "CUC"],
            [
                "nombre" => "Peso Cubano",
                "simbolo" => "₱",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "DOP"],
            [
                "nombre" => "Peso Dominicano",
                "simbolo" => "RD$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "HTG"],
            [
                "nombre" => "Gourde Haitiano",
                "simbolo" => "G",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "HNL"],
            [
                "nombre" => "Lempira Hondureña",
                "simbolo" => "L",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "MXN"],
            [
                "nombre" => "Peso Mexicano",
                "simbolo" => "$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "NIO"],
            [
                "nombre" => "Córdoba Nicaragüense",
                "simbolo" => "C$",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "PAB"],
            [
                "nombre" => "Balboa Panameña",
                "simbolo" => "B/.​",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "PEN"],
            [
                "nombre" => "SOl Peruano",
                "simbolo" => "S/.​",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "PYG"],
            [
                "nombre" => "Guaraní Paraguayo",
                "simbolo" => "₲​",
                "equivalenciaDolar" => 1
            ]
        );
        \App\Coin::updateOrCreate(
            ["codigo" => "UYU"],
            [
                "nombre" => "Peso Uruguayo",
                "simbolo" => "$​",
                "equivalenciaDolar" => 1
            ]
        );
        
    }
}
