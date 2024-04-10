<?php

namespace App\Http\Resources\Game;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class BattleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resutlText = "";
        
        if($this->result == Auth::id()){
            $resutlText = "Win.";
        }else{
            $resutlText = "Lose.";
        }

        return [
            'id' => $this->id,
            'lost_ships' => $this->number_lost_ships,
            'result' => $resutlText,
            'exp' => $this->experience,
            'battle_points' => $this->battle_points,
            'users' => $this->users,
            'date' => $this->created_at->format('Y-m-d H:m')
        ];
    }
}
