<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\World;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\WorldNotPaginationCollection;

class GetAllWorldsController extends Controller
{
    public function index(){

        $worlds = new WorldNotPaginationCollection(World::all());
        return response()->json([
            'success' => true,
            'message' => 'List data worlds',
            'data' => $worlds,
        ], 200);
    }
}
