<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Role::updateOrCreate(["descripcion" => 'Agente'],);
        $agente = \App\Role::whereDescripcion("Agente")->first();
        if($agente != null){
            $entidades = \App\Entity::whereIn("descripcion", ["Clientes", "Cajas", "Prestamos"])->get();
            $entidades = collect($entidades)->map(function($data){
                return $data["id"];
            });
            
            $permisos = \App\Permission::whereIn("idEntidad", $entidades)->get();
            $permisos = collect($permisos)->map(function($d) use($agente){
                return ['idPermiso' => $d['id'], 'idRol' => $agente["id"]];
            });
            $agente->permisos()->attach($permisos);
        }

        \App\Role::updateOrCreate(["descripcion" => 'Supervisor']);
        $agente = \App\Role::whereDescripcion("Agente")->first();
        if($agente != null){
            $entidades = \App\Entity::whereIn("descripcion", ["Clientes", "Cajas", "Prestamos", "Pagos"])->get();
            $entidades = collect($entidades)->map(function($data){
                return $data->id;
            });
            
            $permisos = \App\Permission::whereIn("idEntidad", $entidades)->get();
            $permisos = collect($permisos)->map(function($d) use($agente){
                return ['idPermiso' => $d['id'], 'idRol' => $agente["id"]];
            });
            $agente->permisos()->attach($permisos);
        }
        
    }
}
