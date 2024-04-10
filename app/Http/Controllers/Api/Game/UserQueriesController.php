<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Query;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\QueryCollection;

class UserQueriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function show($id){
        $queries = new QueryCollection(Query::where('user_id', $id)->get());

        return response()->json([
            'success' => true,
            'message' => 'List data login user queries',
            'data' => $queries,
        ], 200);
    }

    public function destroy(Query $query){
        if ($query) {
            $query->delete();

            return response()->json([
                'success' => true,
                'message' => 'Query deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Query not found'
        ], 404);
    }
}
