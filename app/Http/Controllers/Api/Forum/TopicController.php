<?php

namespace App\Http\Controllers\Api\Forum;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Forum\TopicResource;
use App\Http\Resources\Forum\TopicCollection;
use App\Http\Requests\Forum\StoreTopicRequest;
use App\Http\Requests\Forum\UpdateTopicRequest;

class TopicController extends Controller
{
    
    public function index(){
        $topics = new TopicCollection(Topic::query()->paginate(10));
        return response()->json([
            'success' => true,
            'message' => 'List data topics',
            'data' => $topics,
        ], 200);
    }

    public function store(StoreTopicRequest $request){
        $topic = new TopicResource(Topic::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Topic created',
            'data' => $topic
        ], 201);
    }

    public function show($id){
        $topicobj = new TopicResource(Topic::find($id));
        return response()->json([
            'success' => true,
            'message' => 'Detail data topic',
            'data' => $topicobj,
        ], 200);
    }

    public function update(UpdateTopicRequest $request, $id){
        $this->authorize('edit-topics');
        $topic = Topic::find($id);
        if ($topic) {
            $topic->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Topic updated',
                'data' => $topic
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Topic not found'
        ], 404);
    }

    public function destroy($id){
        $this->authorize('delete-topics');
        $topic = Topic::find($id);
        if ($topic) {
            $topic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Topic deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Topic not found'
        ], 404);
    }
}
