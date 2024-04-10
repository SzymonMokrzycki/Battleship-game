<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\EquipmentResource;
use App\Http\Resources\Game\EquipmentCollection;
use App\Http\Requests\Game\StoreEquipmentRequest;
use App\Http\Requests\Game\UpdateEquipmentRequest;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-update-delete-equipments');
        $equipments = Equipment::query()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'List data equipments',
            'data' => $equipments,
        ], 200);
    }

    public function store(StoreEquipmentRequest $request){
        $this->authorize('create-update-delete-equipments');
        $equipment = new EquipmentResource(Equipment::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Equipment created',
            'data' => $equipment
        ], 201);
    }

    public function show(Equipment $equipment){
        $this->authorize('create-update-delete-equipments');
        $equipmentobj = new EquipmentResource($equipment);
        return response()->json([
            'success' => true,
            'message' => 'Detail data posts',
            'data' => $equipmentobj,
        ], 200);
    }

    public function update(UpdateEquipmentRequest $request, $id){
        $this->authorize('create-update-delete-equipments');

        $equipment = Equipment::find($id);

        if ($equipment) {
            $equipment->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Equipment updated',
                'data' => $equipment
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Equipment not found'
        ], 404);
    }

    public function destroy(Equipment $equipment){
        $this->authorize('create-update-delete-equipments');
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
