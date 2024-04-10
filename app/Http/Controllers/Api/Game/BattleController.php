<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Battle;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\BattleResource;
use App\Http\Resources\Game\BattleCollection;
use App\Http\Requests\Game\StoreBattleRequest;

class BattleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-update-delete-battles');
        $battles = Battle::query()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'List data battles',
            'data' => $battles,
        ], 200);
    }

    public function store(StoreBattleRequest $request){
        $this->authorize('create-update-delete-battles');
        $battle = new BattleResource(Battle::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Battle created',
            'data' => $battle
        ], 201);
    }

    public function show(Battle $battle){
        $this->authorize('create-update-delete-battles');
        $battleobj = new BattleResource($battle);
        return response()->json([
            'success' => true,
            'message' => 'Detail data battles',
            'data' => $battleobj,
        ], 200);
    }

    public function destroy(Battle $battle){
        $this->authorize('create-update-delete-battles');
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
