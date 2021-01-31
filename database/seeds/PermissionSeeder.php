<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entidad = \App\Entity::whereDescripcion("Dashboard")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Dashboard', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Clientes")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Prestamos")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);
        // \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Cajas")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Abrir', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Ver cierres', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Hacer transferencias', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Bancos")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Cuentas")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Rutas")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Ver', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Guardar', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Eliminar', "idEntidad" => $entidad->id],);

        $entidad = \App\Entity::whereDescripcion("Configuraciones")->first();
        \App\Permission::updateOrCreate(["descripcion" => 'Empresa', "idEntidad" => $entidad->id],);
        \App\Permission::updateOrCreate(["descripcion" => 'Prestamo', "idEntidad" => $entidad->id],);
    }
}
