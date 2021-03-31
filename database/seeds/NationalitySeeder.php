<?php

use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Argentino"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Boliviano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Chileno"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Colombiano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Costarricense"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Cubano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Dominicano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Ecuatoriano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Español"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Guatemalteco"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Haitiano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Hondureño"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Mexicano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Nicaragüense"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Panameño"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Peruano"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Puertorriqueño"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Paraguayo"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Salvadoreño"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Estadounidense"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Uruguayo"]
        );
        \App\Nationality::updateOrCreate(
            ["descripcion" => "Venenzolano"]
        );
    }
}
