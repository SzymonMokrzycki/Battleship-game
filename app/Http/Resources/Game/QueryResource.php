<?php

namespace App\Http\Resources\Game;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class QueryResource extends JsonResource
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
            'sender_id' => $this->sender_id,
            'sender_fleet' => $this->sender_fleet,
            'sender' => $this->sender,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'date' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
