<?php

namespace App\Http\Controllers\Api\Game;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\Controller;

class SaveUserSkillsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function store(Request $request){
        $file = File::get(storage_path('app/Gamedata/UserSkills/usersdataskills.json'));
        $json = json_decode($file);
        
        $data = [
            "id" => $request->user_id,
            "world_id" => $request->world_id,
            "skill_points" => $request->skill_points,
            "shooting" => $request->shooting,
            "accuracy" => $request->accuracy,
            "commanding" => $request->commanding,
            "shipbuilding" => $request->shipbuilding,
        ];

        array_push($json, $data);
        
        Storage::put('Gamedata\UserSkills\usersdataskills.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Save user skills success',
            'data' => $data
        ], 201);
    }

    public function show($world_id, $user_id){
        $file = File::get(storage_path('app/Gamedata/UserSkills/usersdataskills.json'));
        $json = json_decode($file);

        $result = [];

        foreach($json as $data){
            if($data->id == $user_id && $data->world_id == $world_id){
                $result = $data;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'User skills',
            'data' => $result,
        ], 200);
    }

    public function update(Request $request, $id){
        if ($id) {
            $file = File::get(storage_path('app/Gamedata/UserSkills/usersdataskills.json'));
            $json = json_decode($file);

            $result = [];

            foreach($json as $data){
                if($data->id == $id && $data->world_id == $request->world_id){
                    $data->skill_points = $request->skill_points;
                    $data->shooting = $request->shooting;
                    $data->accuracy = $request->accuracy;
                    $data->commanding = $request->commanding;
                    $data->shipbuilding = $request->shipbuilding;

                    $result = $data;
                }
            }

            Storage::put('GameData\UserSkills\usersdataskills.json', json_encode($json, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Update user skills success',
                'data' => $result
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'File not found'
        ], 404);
    }
}
