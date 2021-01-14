<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            "mensaje" => "",
            "roles" => \App\Http\Resources\RoleResource::collection(Role::take(20)->get()),
            "entidades" => \App\Http\Resources\EntityResource::collection(\App\Entity::take(50)->get())
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            "data.id" => "",
            "data.descripcion" => "",
            "data.permisos" => "",
        ])["data"];


        $role = Role::updateOrCreate(
            ["id" => $data["id"]],
            [
                "descripcion" => $data["descripcion"],
            ]
        );

        $permisos = collect($data["permisos"])->map(function($d) use($role){
            return ["idPermiso" => $d["id"], "idRol" => $role->id];
        });

        $role->permisos()->detach();
        $role->permisos()->attach($permisos);

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "rol" => new \App\Http\Resources\RoleResource($role)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $data = request()->validate([
            "data.id" => "required",
            "data.descripcion" => "",
        ])["data"];

        $role = Role::whereId($data["id"])->first();
        if($role){
            $role->delete();
        }

        return Response::json([
            "mensaje" => "Se ha guardado correctamente",
            "rol" => new \App\Http\Resources\RoleResource($role)
        ]);
    }
}
