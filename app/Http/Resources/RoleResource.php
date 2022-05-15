<?php

namespace App\Http\Resources;

use App\Entity;
use App\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
//        $data = \DB::select("
//        select
//            (select JSON_ARRAYAGG(JSON_OBJECT('id', p.id, 'descripcion', p.descripcion)) from permissions p inner join permission_role pr on p.id = pr.idPermiso where pr.idRol = $this->id) as permisos,
//            (select GROUP_CONCAT(p.descripcion SEPARATOR ', ') from (select concat(e.descripcion, '(', GROUP_CONCAT(IF(p.idEntidad = e.id, p.descripcion, null) SEPARATOR ' • '), ')') as descripcion
//        from (select * from entities where id in(select permissions.idEntidad from permissions where permissions.id in(select pr.idPermiso from permission_role pr where pr.idRol = $this->id))) e
//        inner join permissions p
//        where p.id in(select pr.idPermiso from permission_role pr where pr.idRol = $this->id)
//        group by e.id) as p) AS permisosString
//        ");

//        $data = \App\Permission::query()
//            ->fromSub(Entity::query()->whereIn("id", $this->permisos()->pluck("idEntidad")))
        $permisos = $this->permisos;

        //Obtenemos las entidades con sus respectivos permisos
        $entidades = Entity::query()
            ->selectRaw("CONCAT(entities.descripcion, '(', GROUP_CONCAT(IF(permissions.idEntidad = entities.id, permissions.descripcion, null) SEPARATOR ' • '), ')') as entidadesPermisosString")
            ->whereIn("entities.id", $permisos->pluck("idEntidad"))
            ->join("permissions", "permissions.idEntidad", "=", "entities.id")
            ->groupBy("entities.id", "entities.descripcion")
            ->whereIn("permissions.id", $permisos->pluck("id"));

        $entidadesPermisosString = Entity::query()->selectRaw("group_concat(e.entidadesPermisosString SEPARATOR ', ') as entidadesPermisosString1")->fromSub($entidades, "e")->pluck("entidadesPermisosString1");

        return [
            "id" => $this->id,
            "descripcion" => $this->descripcion,
            "idEntidad" => $this->idEntidad,
            // "permisos" => \App\Http\Resources\PermissionResource::collection($this->permisos),
            "permisos" => $permisos,
            "permisosString" => $entidadesPermisosString[0],
        ];
    }
}
