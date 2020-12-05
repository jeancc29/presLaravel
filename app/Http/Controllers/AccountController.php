<?php

namespace App\Http\Controllers;

use App\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 


class AccountController extends Controller
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
            "accounts" => \App\Http\Resources\AccountResource::collection(Account::take(20)->get()),
            "banks" => \App\Bank::take(50)->get()
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
            "data.idBank" => "",
        ])["data"];


        $account = Account::updateOrCreate(
            ["id" => $data["id"]],
            [
                "description" => $data["description"],
                "idBank" => $data["idBank"],
            ]
        );

        return Response::json([
            "message" => "Se ha guardado correctamente",
            "account" => new \App\Http\Resources\AccountResource($account)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        $data = request()->validate([
            "data.id" => "required",
            "data.description" => "",
        ])["data"];

        $account = Account::whereId($data["id"])->first();
        if($account != null){
            $account->delete();

            return Response::json([
                "message" => "La cuenta se ha eliminado correctamente",
                "account" => $account
            ]);
        }else{
            \abort(402, "La cuenta no existe");
        }
    }
}
