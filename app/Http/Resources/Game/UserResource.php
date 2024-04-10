<?php

namespace App\Http\Resources\Game;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isBanned = false;
        
        if(Carbon::now() < Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($this->banned_to)))){
            $isBanned = true;
        }

        $file = File::get(storage_path('app/Gamedata/UserData/usersdataworld.json'));
        $json = json_decode($file);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'banned_time' => Carbon::now()->diffInDays($this->banned_to),
            'avatar' => $this->avatar,
            'isBanned' => $isBanned
        ];
    }
}
