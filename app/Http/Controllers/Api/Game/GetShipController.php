<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Ship;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class GetShipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $ships = Ship::all();
        return response()->json([
            'success' => true,
            'message' => 'List data ships',
            'data' => $ships,
        ], 200);
    }
}
