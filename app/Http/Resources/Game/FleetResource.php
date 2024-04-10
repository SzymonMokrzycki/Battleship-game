<?php

namespace App\Http\Resources\Game;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FleetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $text = "";

        if($this->ships->count() < 10){
            $text = "Fleet incomplete.";
        }else{
            $text = "Fleet complete.";
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_ships' => $this->number_of_ships,
            'user' => User::find($this->user_id)->name,
            'ships' => $this->ships,
            'number_of_ships' => $this->ships->count(),
            'information' => $text
        ];
    }
}
