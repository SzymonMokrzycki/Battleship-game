<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\User;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\EquipmentResource;

class UserEquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $equipment = new EquipmentResource(User::find($id)->equipment);

        return response()->json([
            'success' => true,
            'message' => 'List data login user equipment',
            'data' => $equipment,
        ], 200);
    }

    public function destroy(Equipment $equipment){
        if ($equipment) {
            $equipment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Equipment deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Equipment not found'
        ], 404);
    }
}
