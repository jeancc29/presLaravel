<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayResource extends JsonResource
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
        // return [
        //     "id" $this->id,
        //     "fecha" $this->fecha,
        //     "capital" $this->capital,
        //     "interes" $this->interes,
        // ]
    }
}
