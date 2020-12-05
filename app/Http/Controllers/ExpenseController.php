<?php

namespace App\Http\Controllers;

use App\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 

class ExpenseController extends Controller
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
            'types' => \App\Type::whereCategory("gasto")->cursor(),
            'boxes' => \App\Box::cursor(),
            'expenses' => \App\Http\Resources\ExpenseResource::collection(Expense::cursor()),
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
            'data.date' => '',
            'data.concept' => '',
            'data.amount' => '',
            'data.commentary' => '',
            'data.idBox' => '',
            'data.idType' => '',
            'data.idUser' => '',
        ])["data"];


        $expense = Expense::updateOrCreate(
            ["id" => $data["id"]],
            [
                "id" => $data["id"],
                "date" => $data["date"],
                "concept" => $data["concept"],
                "amount" => $data["amount"],
                "commentary" => $data["commentary"],
                "idBox" => $data["idBox"],
                "idType" => $data["idType"],
                "idUser" => $data["idUser"],
            ]
        );

        return Response::json([
            "message" => "Se ha guardado correctamente",
            "data" => new \App\Http\Resources\ExpenseResource($expense)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $data = request()->validate([
            'data.id' => '',
            'data.date' => '',
            'data.concept' => '',
            'data.amount' => '',
            'data.commentary' => '',
            'data.idBox' => '',
            'data.idType' => '',
            'data.idUser' => '',
        ])["data"];

        $expense = Expense::whereId($data["id"])->first();
        if($expense != null){
            $expense->delete();
        }

        return Response::json([
            "message" => "Se ha eliminado correctamente",
            "expense" => $expense
        ]);
    }
}
