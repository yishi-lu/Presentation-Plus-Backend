<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller; 

use App\Contracts\Business\CommentService;
use App\Contracts\Constant;

class CommentsController extends Controller
{
    protected $service;
    protected $successStatus = 200;

    public function __contructor(CommentService $service){

        $this->service = $service;
    }

    
    public function create(Request $reqeust){

        $user = Auth::user();

        $this->commentValidator($reqeust);

        $comment = $this->service->createComment($request);

        if($comment){
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' create comment successfully');

            return response()->json(['success'=>$comment], $this->successStatus); 
        }
        else {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' failed to create comment ');

            return response()->json(['message'=>['Failed to create comment']], 401);
        }

    }

    protected function commentValidator(Request $request){

        $this->validate($request, [
            'title' => "required",
            'content' => "required",
        ]); 
    }

}
