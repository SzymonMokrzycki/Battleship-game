<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\User;
use App\Models\World;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\WorldNotPaginationCollection;

class LoginWorldsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show($id){
        $worlds = new WorldNotPaginationCollection(User::find($id)->worlds);
        $starts = [];
        
        foreach($worlds as $world){
            array_push($starts, $world->pivot->created_at);
        }

        $array = array("worlds" => $worlds, "start_at" => $starts);

        return response()->json([
            'success' => true,
            'message' => 'List data login user worlds',
            'data' => $array,
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
