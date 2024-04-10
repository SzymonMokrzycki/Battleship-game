<?php

namespace App\Http\Controllers\Api\Forum;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Forum\PostCollection;

class TopicPostsController extends Controller
{
    
    public function show($topic_id){
        $posts = new PostCollection(Post::where('topic_id', $topic_id)->orderBy('created_at', 'desc')->paginate(10));

        return response()->json([
            'success' => true,
            'message' => 'List data topic posts',
            'data' => $posts,
        ], 200);
    }
}
