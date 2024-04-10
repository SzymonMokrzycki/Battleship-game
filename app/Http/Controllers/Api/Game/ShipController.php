<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Ship;
use App\Models\Fleet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\ShipResource;
use App\Http\Resources\Game\ShipCollection;
use App\Http\Requests\Game\UpdateShipRequest;

class ShipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-update-delete-ships');
        $ships = Ship::query()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'List data ships',
            'data' => $ships,
        ], 200);
    }

    public function show($ship){
        $this->authorize('create-update-delete-ships');
        $shipobj = new ShipResource(Ship::find($ship));
        return response()->json([
            'success' => true,
            'message' => 'Detail data ships',
            'data' => $shipobj,
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

    public function update(UpdateShipRequest $request, $id){
        $this->authorize('create-update-delete-ships');
        $score = $this->readScore(Auth::id());
        
        $fleet = Fleet::find($request->fleet_id);
        $ship = Ship::find($id);

        if ($ship->exists() && $score >= $ship->price) {

            $fleet->ships()->syncWithoutDetaching([$ship->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ship updated',
                'data' => $ship
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Ship not found'
        ], 404);
    }

    public function updateShip(UpdateShipRequest $request, $id){
        $this->authorize('create-update-delete-ships');

        $ship = Ship::find($id);

        if($ship){

            $ship->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ship updated',
                'data' => $ship
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Ship not found'
        ], 404);
    }

    public function destroy(Ship $ship){
        $this->authorize('create-update-delete-ships');
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
