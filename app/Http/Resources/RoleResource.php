<?php

namespace App\Http\Resources;

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
        $data = \DB::select("select (select JSON_ARRAYAGG(JSON_OBJECT('id', p.id, 'descripcion', p.descripcion)) from permissions p inner join permission_role pr on p.id = pr.idPermiso where pr.idRol = $this->id) as permisos, 
        (select GROUP_CONCAT(p.descripcion SEPARATOR ', ') from (select concat(e.descripcion, '(', GROUP_CONCAT(IF(p.idEntidad = e.id, p.descripcion, null) SEPARATOR ' • '), ')') as descripcion
from (select * from entities where id in(select permissions.idEntidad from permissions where permissions.id in(select pr.idPermiso from permission_role pr where pr.idRol = $this->id))) e 
inner join permissions p
where p.id in(select pr.idPermiso from permission_role pr where pr.idRol = $this->id)
group by e.id) as p) AS permisosString
        ");
        return [
            "id" => $this->id,
            "descripcion" => $this->descripcion,
            "idEntidad" => $this->idEntidad,
            // "permisos" => \App\Http\Resources\PermissionResource::collection($this->permisos),
            "permisos" => $data[0]->permisos,
            "permisosString" => $data[0]->permisosString,
        ];
    }
}
