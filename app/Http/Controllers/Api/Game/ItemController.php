<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Item;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\ItemResource;
use App\Http\Resources\Game\ItemCollection;
use App\Http\Requests\Game\UpdateItemRequest;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $items = Item::all();
        return response()->json([
            'success' => true,
            'message' => 'List data items',
            'data' => $items,
        ], 200);
    }

    public function show($id){
        $this->authorize('update-delete-items');
        $item = new ItemResource(Item::find($id));
        return response()->json([
            'success' => true,
            'message' => 'Detail data items',
            'data' => $item,
        ], 200);
    }

    private function readScore($user_id){
        $file = File::get(storage_path('app/Gamedata/UserData/usersdataworld.json'));
        $json = json_decode($file);
        foreach($json as $object){
            if($object->id == $user_id){
                $score = $object->battlescore;
            }
        }

        return $score;
    }
    
    public function update(UpdateItemRequest $request, $id){
        $this->authorize('update-delete-items');
        $score = $this->readScore(Auth::id());
        
        $equipment = Equipment::find($request->equipment_id);
        $item = Item::find($id);
        
        if ($item->exists() && $score >= $item->price) {

            $equipment->items()->syncWithoutDetaching([$item->id]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated',
                'data' => $item
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }

    public function destroy(Item $item){
        $this->authorize('update-delete-items');
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
    
    public function deleteItem(Request $request, $id){
        $item = Item::find($id);

        if($item){
            $item->equipment()->detach([$request->equipment_id ,$id]);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted from equipment'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Item not found'
        ], 404);
    }
}
