<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\World;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\UserCollection;

class WorldUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $users = World::find($id)->users;

        return response()->json([
            'success' => true,
            'message' => 'List data world users',
            'data' => $users,
        ], 200);
    }

    public function destroy(World $world){
        if ($world) {
            $world->delete();

            return response()->json([
                'success' => true,
                'message' => 'World deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'World not found'
        ], 404);
    }
}
