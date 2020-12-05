<?php

namespace App\Http\Controllers;

use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            'message' => '',
            'routes' => \App\Route::cursor(),
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
            'data.id' => '',
            'data.description' => 'required',
        ])["data"];

        $route = Route::updateOrCreate(
            ["id" => $data["id"]],
            [
                "description" => $data["description"],
            ]
        );
        return Response::json([
            "route" => $route,
            "message" => "Se ha guardado correctamente",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function edit(Route $route)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Route $route)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function destroy(Route $route)
    {
        $data = request()->validate([
            'data.id' => '',
            'data.description' => '',
        ])["data"];

        try {
            $route = Route::whereId($data['id'])->first();
            if($route != null)
            {
                $route->delete();
                return Response::json([
                    "message" => "Se ha eliminado correctamente",
                    "route" => $route
                ]);
            }else{
                return Response::json([
                    "message" => "route no existe",
                    "error" => 1,
                    "route" => $data
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            \abort(400, "Error: " . $th);
        }

    }
}
