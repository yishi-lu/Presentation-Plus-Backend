<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Input; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Log;
use App\User; 
use App\POST; 

use App\Contracts\Business\BusinessService;
use App\Contracts\Constant;

class PostsController extends Controller
{
    protected $service;
    public $successStatus = 200;

    /**
     * PostsController constructor.
     * @param $service
     */
    public function __construct(BusinessService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request){

        $validator = $this->postValidation($request);

        $post = $this->service->createOnePost($request);

        $user = Auth::user();

        if($post){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' created post successfully');

            return response()->json(['success'=>$post], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to create post ');

            return response()->json(['message'=>['Failed to create post']], 401);
        }
    }

    public function edit(Request $request){

        $user = Auth::user();

        $validator = $this->postValidation($request);

        $result = $this->service->editOnePost($request);


        if($result){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' edited post successfully');

            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to edit post ');

            return response()->json(['message'=>['Failed to edit post']], 401);
        }

    }

    public function delete(Request $request){

        $user = Auth::user();

        $result = $this->service->deletePost($request);

        if($result){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' deleted post successfully');

            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to delete post ');

            return response()->json(['message'=>['Failed to delete post']], 401);
        }

    }

    public function detail($id){

        $post = $this->service->fetchOnePost($id);

        if($post){
            Log::debug('Fetch post '. $id .' successfully');

            return response()->json(['success'=>$post], $this->successStatus); 
        }
        else {
            Log::debug('Failed to fetch post '. $id);

            return response()->json(['message'=>['Failed to fetch the post']], 401);
        }
    }

    public function fetchAllPosts(Request $request){

        $posts = $this->service->fetchAllPosts($request->get('page'));

        if($posts){
            Log::debug('Fetch all posts successfully');

            return response()->json(['success'=>$posts], $this->successStatus); 
        }
        else {
            Log::debug('Failed to all post');

            return response()->json(['message'=>['Failed to fetch all post']], 401);
        }
    }

    public function fetchUserPosts($id, Request $request){

        $user = User::find($id);

        $posts = $this->service->fetchUserPosts($user, $request->get('page'));

        if($posts){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' posts are all fetched');

            return response()->json(['success'=>$posts], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to fetch all posts ');

            return response()->json(['message'=>['Failed to fetch post']], 401);
        }
    }

    public function collectPost(Request $request){

        $user = Auth::user();

        $posts = $this->service->collectPost($request->get('post_id'));

        if($posts){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' posts are all fetched');

            return response()->json(['success'=>$posts], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to fetch all posts ');

            return response()->json(['message'=>['Failed to fetch post']], 401);
        }
    }

    public function fetchCollectedPosts(){

        $posts = $this->service->fetchCollectedPosts();

        if($posts){
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' posts are all fetched');

            return response()->json(['success'=>$posts], $this->successStatus); 
        }
        else {
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to fetch all posts ');

            return response()->json(['message'=>['Failed to fetch post']], 401);
        }

    }

    public function thumbPost(Request $request){

        $user = Auth::user();

        $posts = $this->service->thumbPost($request->get('post_id'));

        if($posts){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' thumb a post');

            return response()->json(['success'=>$posts], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to thumb a post ');

            return response()->json(['message'=>['Failed to fetch post']], 401);
        }
    }

    protected function postValidation(Request $request){
        $this->validate($request, [
            'title' => "required",
            'content' => "required",
        ]);
    }
}
