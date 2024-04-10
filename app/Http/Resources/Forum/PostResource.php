<?php

namespace App\Http\Resources\Forum;

use App\Models\User;
use App\Models\Topic;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'message' => $this->message,
            'likes' => $this->likes,
            'author' => User::find($this->user_id)->name,
            'topic' => Topic::find($this->topic_id)->name,
            'avatar' => User::find($this->user_id)->avatar,
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
