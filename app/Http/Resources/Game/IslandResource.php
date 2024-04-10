<?php

namespace App\Http\Resources\Game;

use App\Models\World;
use Illuminate\Http\Resources\Json\JsonResource;

class IslandResource extends JsonResource
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
            'world' => World::find($this->world_id)->name
        ];
    }
}
