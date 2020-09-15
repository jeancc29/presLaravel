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
        \App\Type::updateOrCreate(["descripcion" => "Ninguna"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Combustible"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Gastos Diversos"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Nómina"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Comisión Agente"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Aportaciones"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Automóvil"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Renta"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Sistema"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Pagina Web"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Imprestos"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Seguro Social"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Comisiones Bancarias"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Misceláneo"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Almuerzo Administrativo"], ["renglon" => "gasto"]);
    }
}
