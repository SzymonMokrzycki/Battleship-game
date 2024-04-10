<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\World;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\WorldResource;
use App\Http\Resources\Game\WorldCollection;
use App\Http\Requests\Game\StoreWorldRequest;
use App\Http\Requests\Game\UpdateWorldRequest;

class WorldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){

        $worlds = new WorldCollection(World::query()->paginate(10));
        return response()->json([
            'success' => true,
            'message' => 'List data worlds',
            'data' => $worlds,
        ], 200);
    }

    public function store(Request $request){
        $this->authorize('create-update-delete-worlds');
        $world = new WorldResource(World::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'World created',
            'data' => $world
        ], 201);
    }

    public function show(World $world){
        $this->authorize('create-update-delete-worlds');
        $worldobj = new WorldResource($world);
        return response()->json([
            'success' => true,
            'message' => 'Detail data worlds',
            'data' => $worldobj,
        ], 200);
    }

    public function update(UpdateWorldRequest $request, $id){
        $this->authorize('create-update-delete-worlds');
        $world = World::find($id);
        if ($world) {
            $world->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'World updated',
                'data' => $world
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'World not found'
        ], 404);
    }

    public function destroy(World $world){
        $this->authorize('create-update-delete-worlds');
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
