<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Island;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class GetIslandsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $islands = Island::all();
        return response()->json([
            'success' => true,
            'message' => 'List data islands',
            'data' => $islands,
        ], 200);
    }
}
