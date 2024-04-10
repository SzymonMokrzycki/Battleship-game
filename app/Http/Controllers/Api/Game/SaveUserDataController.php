<?php

namespace App\Http\Controllers\Api\Game;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\Controller;

class SaveUserDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request){
        $file = File::get(storage_path('app/Gamedata/UserData/usersdataworld.json'));
        $json = json_decode($file);
        
        $data = [
            "id" => $request->user_id,
            "world_id" => $request->world_id,
            "lvl" => $request->lvl,
            "rank" => $request->rank,
            "exp" => $request->exp,
            "battlescore" => $request->battlescore,
        ];
        
        array_push($json, $data);

        Storage::put('Gamedata\UserData\usersdataworld.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Save user data success',
            'data' => $data
        ], 201);
    }

    public function show($world_id, $user_id){
        $file = File::get(storage_path('app/Gamedata/UserData/usersdataworld.json'));
        $json = json_decode($file);

        $result = [];

        foreach($json as $data){
            if($data->id == $user_id && $data->world_id == $world_id){
                $result = $data;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Game data user',
            'data' => $result,
        ], 200);
    }

    public function update(Request $request, $id){
        if ($id) {
            $file = File::get(storage_path('app/Gamedata/UserData/usersdataworld.json'));
            $json = json_decode($file);


            foreach($json as $data){
                if($data->id == $id && $data->world_id == $request->world_id){
                    $data->id = intval($id);
                    $data->world_id = $request->world_id;
                    $data->lvl = $request->lvl;
                    $data->rank = $request->rank;
                    $data->exp = $request->exp;
                    $data->battlescore = $request->battlescore;
                }
            }
            
            Storage::put('Gamedata\UserData\usersdataworld.json', json_encode($json));

            return response()->json([
                'success' => true,
                'message' => 'Update user data success',
                'data' => $json
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'File not found'
        ], 404);
    }
}
