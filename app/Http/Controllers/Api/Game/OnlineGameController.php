<?php

namespace App\Http\Controllers\Api\Game;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Fleet;
use App\Models\Query;
use Illuminate\Http\Request;
use App\Enums\GameStatusEnum;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\Controller;

class OnlineGameController extends Controller
{
    public function show($id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        $data = [];

        foreach($json as $object){
            if($object->id == $id){
                $data = $object;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Read data game',
            'data' => $data
        ], 200);
    }
    
    public function store(Request $request, $id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        $invitation = Query::find($id);

        $senderPositions = (object) [];
        $recipientPositions = (object) [];

        $sender_ships_hp = [];
        $recipient_ships_hp = [];

        foreach(Fleet::find($request->sender_fleet_id)->ships as $ship){
            $sender_ships_hp += [
                strval($ship->id) => $ship->hp
            ];
        }

        foreach(Fleet::find($request->recipient_fleet_id)->ships as $ship){
            $recipient_ships_hp += [
                strval($ship->id) => $ship->hp
            ];
        }

        $data = [
            "id" => intval($id),
            "sender_id" => $invitation->sender_id,
            "recipient_id" => $invitation->user_id,
            "sender_name" => $invitation->sender,
            "recipient_name" => User::find($invitation->user_id)->name,
            "game_time" => Carbon::now()->format('Y-m-d H:i'),
            "game_status" => GameStatusEnum::WAITING,
            "ships_sender" => Fleet::find($request->sender_fleet_id)->ships,
            "ships_recipient" => Fleet::find($request->recipient_fleet_id)->ships,
            "result" => 0,
            "ships_recipient_hp" => $recipient_ships_hp,
            "ships_sender_hp" => $sender_ships_hp,
            "current_moving" => $invitation->user_id,
            "sender_positions" => $senderPositions,
            "recipient_positions" => $recipientPositions,
            "last_move" => "-",
            "round" => 1
        ];

        array_push($json, $data);

        Storage::put('public\OnlineGame\OnlineGameFile.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Create game data',
            'data' => $data
        ], 201);
    }

    public function storePositions(Request $request, $id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        foreach($json as $object){
            if($object->id == $id){
                if($request->sender_positions != null){
                    $object->sender_positions = $request->sender_positions;
                }

                if($request->recipient_positions != null){
                    $object->recipient_positions = $request->recipient_positions;
                }
            }   
        }

        Storage::put('public\OnlineGame\OnlineGameFile.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Update game data success',
            'data' => $json
        ], 200);
    }

    public function storeMove(Request $request, $id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        foreach($json as $object){
            if($object->id == $id){

                if($request->user_name == $object->recipient_name){
                    $object->ships_recipient = $request->ships;
                }

                if($request->user_name == $object->sender_name){
                    $object->ships_sender = $request->ships;
                }

                $object->current_moving = $request->current_moving;
                $object->last_move = $request->last_move;
                $object->round = $request->round;
            }   
        }

        Storage::put('public\OnlineGame\OnlineGameFile.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Update game data success',
            'data' => $json
        ], 200);
    }

    public function updateStatus($id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        foreach($json as $object){
            if($object->id == $id){
                $object->game_status = GameStatusEnum::DURINGTHEGAME;
            }   
        }

        Storage::put('public\OnlineGame\OnlineGameFile.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Update game data success',
            'data' => $json
        ], 200);
    }

    public function gameOver(Request $request, $id){
        $file = File::get(storage_path('app/public/OnlineGame/OnlineGameFile.json'));
        $json = json_decode($file);

        foreach($json as $object){
            if($object->id == $id){
                $object->game_status = GameStatusEnum::GAMEOVER;
                $object->result = $request->result;
            }   
        }

        Storage::put('public\OnlineGame\OnlineGameFile.json', json_encode($json, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Update game data success',
            'data' => $json
        ], 200);
    }
}
