<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            "message" => "",
            "banks" => Bank::take(20)->get(),
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
            "data.description" => "",
            "data.status" => "",
        ])["data"];


        $bank = Bank::updateOrCreate(
            ["id" => $data["id"]],
            [
                "description" => $data["description"],
                "status" => $data["status"],
            ]
        );

        return Response::json([
            "message" => "Se ha guardado correctamente",
            "bank" => $bank
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $data = request()->validate([
            "data.id" => "required",
            "data.description" => "",
        ])["data"];

        $bank = Bank::whereId($data["id"])->first();
        if($bank != null){
            $bank->delete();

            return Response::json([
                "message" => "El banco se ha eliminado correctamente",
                "bank" => $bank
            ]);
        }else{
            \abort(402, "La banco no existe");
        }
    }
}
