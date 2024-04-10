<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Island;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\IslandResource;
use App\Http\Resources\Game\IslandCollection;
use App\Http\Requests\Game\StoreIslandRequest;

class IslandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $islands = new IslandCollection(Island::query()->paginate(10));
        return response()->json([
            'success' => true,
            'message' => 'List data islands',
            'data' => $islands,
        ], 200);
    }

    public function store(StoreIslandRequest $request){
        $this->authorize('create-delete-islands');
        $island = new IslandResource(Island::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Island created',
            'data' => $island
        ], 201);
    }

    public function show(Island $island){
        $this->authorize('create-delete-islands');
        $islandobj = new IslandResource($island);
        return response()->json([
            'success' => true,
            'message' => 'Detail data islands',
            'data' => $islandobj,
        ], 200);
    }
    
    public function destroy($id){
        $this->authorize('create-delete-islands');
        $island = Island::find($id);
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
