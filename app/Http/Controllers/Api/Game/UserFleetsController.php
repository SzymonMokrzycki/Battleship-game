<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Fleet;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\FleetResource;
use App\Http\Resources\Game\FleetCollection;

class UserFleetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $fleets = new FleetCollection(Fleet::where('user_id', $id)->get());

        return response()->json([
            'success' => true,
            'message' => 'List data login user fleets',
            'data' => $fleets,
        ], 200);
    }

    public function destroy(Fleet $fleet){
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
