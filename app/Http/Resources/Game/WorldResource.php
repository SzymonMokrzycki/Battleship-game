<?php

namespace App\Http\Resources\Game;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WorldResource extends JsonResource
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
            'status' => $this->status,
            'age' => Carbon::now()->diffInDays($this->created_at),
            'players' => count($this->users)
        ];
    }
}
