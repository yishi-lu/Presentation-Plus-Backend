<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller; 

use App\Contracts\Business\CommentService;
use App\Contracts\Constant;

class CommentsController extends Controller
{
    protected $service;
    protected $successStatus = 200;

    public function __construct(CommentService $service){

        $this->service = $service;
    }

    
    public function create(Request $requst){

        $user = Auth::guard('api')->user();;

        $this->commentValidator($requst);
        
        $comment = $this->service->createComment($requst);

        if($comment){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' create comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to create comment ');

            return response()->json(['message'=>['Failed to create comment']], 401);
        }

    }

    public function edit(Request $request){

        $user = Auth::guard('api')->user();;

        $this->commentValidator($request);

        $comment = $this->service->editComment($request);

        if($comment){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' edit comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to edit comment ');

            return response()->json(['message'=>['Failed to edit comment']], 401);
        }


    }

    public function fetchPostComments($id, Request $request){

        $current_page = $request->get("mainCommentPage");

        $comment = $this->service->fetchPostComments($id, $current_page);

        if($comment){
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' edit comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to edit comment ');

            return response()->json(['message'=>['Failed to edit comment']], 401);
        }
    }

    public function fetchUserComments($id){

        $comment = $this->service->fetchUserComments($id);

        if($comment){
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' edit comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to edit comment ');

            return response()->json(['message'=>['Failed to edit comment']], 401);
        }
    }

    public function likeComment(Request $request){

        $comment = $this->service->likeComment($request->get('comment_id'));

        if($comment){
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' edit comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            // Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to edit comment ');

            return response()->json(['message'=>['Failed to like comment']], 401);
        }
    }

    protected function commentValidator(Request $request){

        $this->validate($request, [
            'title' => "required",
            'content' => "required",
        ]); 
    }

}
