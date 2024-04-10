<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Crew;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class UserCrewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show($id){
        $crews = Crew::where('user_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'List data login user crews',
            'data' => $crews,
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
