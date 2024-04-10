<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Island;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class WorldIslandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show($id){
        $islands = Island::where('world_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'List data world islands',
            'data' => $islands,
        ], 200);
    }

    public function destroy(Island $island){
        if ($island) {
            $island->delete();

            return response()->json([
                'success' => true,
                'message' => 'Island deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Island not found'
        ], 404);
    }
}
