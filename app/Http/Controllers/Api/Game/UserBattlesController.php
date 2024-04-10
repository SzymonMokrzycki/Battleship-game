<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\BattleCollection;
use App\Models\Battle;

class UserBattlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $battles = new BattleCollection(Battle::where('user_id', $id)->paginate(10));

        return response()->json([
            'success' => true,
            'message' => 'List data login user battles',
            'data' => $battles,
        ], 200);
    }

    public function destroy(Battle $battle){
        if ($battle) {
            $battle->delete();

            return response()->json([
                'success' => true,
                'message' => 'Battle deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Battle not found'
        ], 404);
    }
}
