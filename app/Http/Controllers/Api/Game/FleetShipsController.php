<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Ship;
use App\Models\Fleet;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\FleetResource;
use App\Http\Resources\Game\ShipCollection;
use App\Http\Resources\Game\FleetCollection;

class FleetShipsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $ships = new FleetResource(Fleet::find($id)->load('ships'));
        return response()->json([
            'success' => true,
            'message' => 'List data fleet ships',
            'data' => $ships,
        ], 200);
    }

    public function destroy(Ship $ship){
        if ($ship) {
            $ship->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ship deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Ship not found'
        ], 404);
    }
}
