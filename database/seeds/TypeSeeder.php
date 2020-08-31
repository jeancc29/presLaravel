<?php

use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Type::updateOrCreate(["descripcion" => "Cedula identidad"], ["renglon" => "documento"]);
        \App\Type::updateOrCreate(["descripcion" => "RNC"], ["renglon" => "documento"]);
        \App\Type::updateOrCreate(["descripcion" => "Pasaporte"], ["renglon" => "documento"]);
    }
}
