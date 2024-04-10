<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\User;
use App\Models\World;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Game\StoreWorldRequest;

class StoreNewWorldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function store(StoreWorldRequest $request){
        $user = User::find($request->user_id);
        $user->worlds()->attach([$request->all()]);
        return response()->json([
            'success' => true,
            'message' => 'Store new world success'
        ], 200);
    }
}
