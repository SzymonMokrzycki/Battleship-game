<?php

namespace App\Http\Controllers\Api\Forum;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Forum\PostResource;
use App\Http\Resources\Forum\PostCollection;
use App\Http\Requests\Forum\StorePostRequest;
use App\Http\Requests\Forum\UpdatePostRequest;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $posts = new PostCollection(Post::query()->paginate(10));
        return response()->json([
            'success' => true,
            'message' => 'List data posts',
            'data' => $posts,
        ], 200);
    }

    public function store(StorePostRequest $request){
        $post = new PostResource(Post::create($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Post created',
            'data' => $post
        ], 201); 
    }

    public function show(Post $post){
        $postobj = new PostResource($post);
        return response()->json([
            'success' => true,
            'message' => 'Detail data posts',
            'data' => $postobj,
        ], 200);
    }

    public function update(UpdatePostRequest $request, $id){
        $this->authorize('edit-posts');
        $post = Post::find($id);
        if ($post) {
            $post->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Post updated',
                'data' => $post
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Post not found'
        ], 404);
    }

    public function destroy($id){
        $this->authorize('delete-posts');
        $post = Post::find($id);
        if ($post) {
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Post not found'
        ], 404);
    }
}
