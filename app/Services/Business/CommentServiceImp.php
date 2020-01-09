<?php

namespace App\Services\Business;

use App\Contracts\Business\CommentService;
use App\User; 
use App\Post; 
use App\Comment; 

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Contracts\Constant;

class CommentServiceImp implements CommentService{

        //fetch all comments of a post
        public function fetchPostComments($paging_info=20){

        }

        //fetch all comments of a user
        public function fetchUserComments($user, $paging_info=20){

        }
    
        //create a comment
        public function createComment($request){

            $user = Auth::user();
            $post = Post::find($request->get("post_id"));
            $target_comment = Comment::find($request->get("comment_id"));

            $comment = null;
            
            if($post && $user){

                $comment = new Comment();

                $comment->title = $request->get("title");
                $comment->content = $request->get("content");
                $comment->liked = 0;
                $comment->status = Constant::STATUS_ACTIVATED;

                $comment->user()->associate($user);
                $comment->post()->associate($post);

                if($target_comment) $comment->commentedOn()->associate($target_comment);
            }

            return $comment;

        }
    
        //edit a given comment
        public function editComment($request){

        }
    
        //delete a comment
        public function deleteComment($request){

        }
    
        //like a comment
        public function likeComment($id){

        }

}