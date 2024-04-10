<?php

namespace App\Http\Controllers\Api\Game;

use App\Models\Query;
use Illuminate\Http\Request;
use App\Enums\QueryStatusEnum;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\QueryResource;
use App\Http\Resources\Game\QueryCollection;
use App\Http\Requests\Game\StoreQueryRequest;
use App\Http\Requests\Game\UpdateQueryRequest;

class QueriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-delete-queries');
        $queries = Query::query()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'List data queries',
            'data' => $queries,
        ], 200);
    }

    public function store(StoreQueryRequest $request){
        $this->authorize('create-delete-queries');
        $query = new QueryResource(Query::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Query created',
            'data' => $query
        ], 201);
    }

    public function show($id){
        $this->authorize('create-delete-queries');
        $queryobj = new QueryResource(Query::find($id));
        return response()->json([
            'success' => true,
            'message' => 'Detail data queries',
            'data' => $queryobj,
        ], 200);
    }

    public function update(UpdateQueryRequest $request, $id){
        $query = Query::find($id);
        if ($query) {

            if($request->status === 'accepted'){
                $query->status = QueryStatusEnum::ACCEPTED;
            }

            if($request->status === 'rejected'){
                $query->status = QueryStatusEnum::REJECTED;
            }

            $query->save();

            return response()->json([
                'success' => true,
                'message' => 'Query updated',
                'data' => $query
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Query not found'
        ], 404);
    }
    
    public function destroy($id){
        $this->authorize('create-delete-queries');
        $query = Query::find($id);
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
