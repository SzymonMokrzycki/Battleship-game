<?php

namespace App\Http\Resources\Game;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'number_items' => $this->number_of_items,
            'items' => $this->items,
            'user' => $this->user->name
        ];
    }
}
