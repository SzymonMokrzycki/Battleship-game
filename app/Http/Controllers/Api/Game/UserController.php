<?php

namespace App\Http\Controllers\Api\Game;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Game\UserResource;
use App\Http\Resources\Game\UserCollection;
use App\Http\Requests\Game\StoreUserRequest;
use App\Http\Requests\Game\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index(){
        $this->authorize('create-update-delete-users');
        $users = new UserCollection(User::query()->paginate(10));
        return response()->json([
            'success' => true,
            'message' => 'List data users',
            'data' => $users,
        ], 200);
    }

    public function store(StoreUserRequest $request){
        $this->authorize('create-update-delete-users');
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "role_id" => $request->role_id
        ];
        $user = User::create($data);
        return response()->json([
            'success' => true,
            'message' => 'User created',
            'data' => $user
        ], 201);
    }

    public function show(User $user){
        $this->authorize('create-update-delete-users');
        $userobj = new UserResource($user);
        return response()->json([
            'success' => true,
            'message' => 'Detail data users',
            'data' => $userobj,
        ], 200);
    }

    public function update(UpdateUserRequest $request, $id){
        $this->authorize('edit-user');
        $user = User::find($id);
        
        if($request->name != null)
            $user->name = $request->name;

        if($request->email != null)
            $user->email = $request->email;

        if($request->hasFile('avatar')){
            $file = $request->file('avatar');
            Storage::disk('public')->put('avatars/'.$file->getClientOriginalName(), $file->get());
            $user->avatar = url('/storage/avatars/'.$file->getClientOriginalName());
        }
            
        if($request->password != null)
            $user->password = bcrypt($request->password);

        if($request->banned_from != null)
            $user->banned_from = date('Y-m-d', strtotime($request->banned_from));

        if($request->banned_to != null)
            $user->banned_to = date('Y-m-d', strtotime($request->banned_to));

        if($request->banned_from != null && $request->banned_to != null)
            $user->banned_time = Carbon::createFromFormat('Y-m-d', $user->banned_to)
            ->diffInDays(Carbon::createFromFormat('Y-m-d', $user->banned_from));
        
        if($user->id == $id){
            
            if ($user) {
                
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'User updated',
                    'data' => $user
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'success' => false,
            'message' => 'You cant update or banned admin user'
        ], 403);
    }

    public function destroy($id){
        $this->authorize('create-update-delete-users');
        $user = User::find($id);
        if ($user) {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }
}
