<?php

namespace App\Http\Resources\Game;

use Illuminate\Http\Request;
use App\Http\Resources\Game\WorldResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WorldNotPaginationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ['data' => WorldResource::collection($this->collection)];
    }
}
