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
        
        $empresa = \App\Company::whereNombre("Prueba")->first();

        \App\Role::updateOrCreate(["descripcion" => 'Agente'], ["idEmpresa" => $empresa->id]);
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

        $agente = \App\Role::updateOrCreate(["descripcion" => 'Supervisor'], ["idEmpresa" => $empresa->id]);
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

        $agente = \App\Role::updateOrCreate(["descripcion" => 'Programador'], ["idEmpresa" => $empresa->id]);
        if($agente != null){
            $entidades = \App\Entity::all();
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
