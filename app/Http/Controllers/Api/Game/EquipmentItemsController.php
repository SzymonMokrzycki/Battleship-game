<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Item;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\ItemCollection;
use App\Http\Resources\Game\EquipmentResource;
use App\Models\User;

class EquipmentItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $items = new EquipmentResource(User::find($id)->equipment);
        return response()->json([
            'success' => true,
            'message' => 'List data equipment items',
            'data' => $items,
        ], 200);
    }

    public function destroy(Item $item){
        if ($item) {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }
}
