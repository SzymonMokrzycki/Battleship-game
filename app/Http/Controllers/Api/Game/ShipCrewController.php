<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Crew;
use App\Models\Ship;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\CrewResource;

class ShipCrewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $crew = new CrewResource(Ship::find($id)->crew);
        return response()->json([
            'success' => true,
            'message' => 'Data ship crew',
            'data' => $crew,
        ], 200);
    }

    public function destroy(Crew $crew){
        if ($crew) {
            $crew->delete();

            return response()->json([
                'success' => true,
                'message' => 'Crew deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Crew not found'
        ], 404);
    }
}
