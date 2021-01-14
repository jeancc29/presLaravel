<?php

use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Entity::updateOrCreate(["descripcion" => 'Dashboard'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Clientes'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Prestamos'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Pagos'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Cajas'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Bancos'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Cuentas'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Rutas'],);
        \App\Entity::updateOrCreate(["descripcion" => 'Configuraciones'],);
    }
}
