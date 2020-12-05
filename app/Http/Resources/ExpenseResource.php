<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
        return [
            "id" => $this->id,
            "concept" => $this->concept,
            "amount" => $this->amount,
            "date" => $this->date,
            "commentary" => $this->commentary,
            "idBox" => $this->idBox,
            "box" => $this->box,
            "idType" => $this->idType,
            "type" => $this->type,
        ];
    }
}
