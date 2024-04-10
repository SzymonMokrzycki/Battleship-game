<?php

namespace App\Http\Resources\Game;

use App\Models\Fleet;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'number_of_canons' => $this->number_of_canons,
            'damage_canons' => $this->damage_canons,
            'strong_crew' => $this->strong_crew,
            'hp' => $this->hp,
            'price' => $this->price,
            'armament' => $this->armament
        ];
    }
}
