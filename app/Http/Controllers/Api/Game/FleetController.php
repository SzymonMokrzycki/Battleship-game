<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Fleet;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\FleetResource;
use App\Http\Resources\Game\FleetCollection;
use App\Http\Requests\Game\StoreFleetRequest;

class FleetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-update-delete-fleets');
        $fleets = Fleet::query()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'List data fleets',
            'data' => $fleets,
        ], 200);
    }

    public function store(StoreFleetRequest $request){
        $this->authorize('create-update-delete-fleets');
        $fleet = new FleetResource(Fleet::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Fleet created',
            'data' => $fleet
        ], 201);
    }

    public function show(Fleet $fleet){
        $this->authorize('create-update-delete-fleets');
        $fleetobj = new FleetResource($fleet);
        return response()->json([
            'success' => true,
            'message' => 'Detail data fleets',
            'data' => $fleetobj,
        ], 200);
    }

    public function update(UpdateFleetRequest $request, Fleet $fleet){
        $this->authorize('create-update-delete-fleets');
        
        if ($fleet) {
            $fleet->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Fleet updated',
                'data' => $fleet
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Fleet not found'
        ], 404);
    }

    public function destroy(Fleet $fleet){
        $this->authorize('create-update-delete-fleets');
        if ($fleet) {
            $fleet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fleet deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Fleet not found'
        ], 404);
    }
}
