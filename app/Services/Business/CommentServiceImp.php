<?php

namespace App\Services\Business;

use Illuminate\Pagination\LengthAwarePaginator;
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
        public function fetchPostComments($post_id, $current_page=1, $paging_info=20){

            $query = DB::table("comments")
                     ->select('comments.id', 'comments.title', 'comments.content', 'comments.user_id', 'comments.post_id', 'comments.status', 'users.name', 'profiles.portrait', 'comments.created_at')
                     ->join('users','users.id','=','comments.user_id')
                     ->join('profiles','profiles.user_id','=','users.id')
                     ->where('comments.comment_id', '=', null)
                     ->where('comments.post_id','=', $post_id)
                     ->where('comments.status', '=', Constant::STATUS_ACTIVATED)
                     ->where('users.status', '=', Constant::STATUS_ACTIVATED)
                     ->orderBy('comments.created_at','desc')
                     ->get();

                    //  $query = $query->paginate($paging_info);
            

            foreach($query as $item){

                $user = Auth::guard('api')->user();

                $id = $item->id;

                $count_liked = DB::table("comment_user")
                               ->select("id")
                               ->where("comment_id", "=", $id)
                               ->count();

                $item->liked = $count_liked;

                if($user){
                    $is_liked = DB::table("comment_user")
                                ->select("id")
                                ->where("comment_id", "=", $id)
                                ->where("user_id", "=", $user->id)
                                ->count();
                            
                    $item->is_liked = $is_liked;
                }

                $sub_comments = DB::table("comments")
                               ->select('comments.id', 'comments.comment_id', 'comments.title', 'comments.content', 'comments.user_id', 'comments.post_id', 'comments.status', 'users.name', 'profiles.portrait', 'comments.created_at')
                               ->join('users','users.id','=','comments.user_id')
                               ->join('profiles','profiles.user_id','=','users.id')
                               ->where('comments.comment_id', '=', $id)
                               ->where('comments.status', '=', Constant::STATUS_ACTIVATED)
                               ->where('users.status', '=', Constant::STATUS_ACTIVATED)
                               ->orderBy('comments.created_at','desc')
                               ->paginate(5);
                
                if($sub_comments != null) {

                    foreach($sub_comments as $sub){
                        $sub_id = $sub->id;

                        $count_liked = DB::table("comment_user")
                                        ->select("id")
                                        ->where("comment_id", "=", $sub_id)
                                        ->count();

                        $sub->liked = $count_liked;

                        if($user){
                            $is_liked = DB::table("comment_user")
                                        ->select("id")
                                        ->where("comment_id", "=", $sub_id)
                                        ->where("user_id", "=", $user->id)
                                        ->count();
                            
                            $sub->is_liked = $is_liked;
                        }

                    }

                    $item->sub_comments = $sub_comments;

                }

            }

            //pagination on collection
            $page = $current_page;
            $perPage = $paging_info;
            $pagination = new LengthAwarePaginator(
                $query->forPage($page, $perPage), 
                $query->count(), 
                $perPage, 
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => "mainCommentPage",
                ]
            );

            return $pagination;

        }

        //fetch all comments of a user
        public function fetchUserComments($user_id, $paging_info=20){

            $query = DB::table("comments")
                     ->select('comments.id', 'comments.title', 'comments.content', 'comments.user_id', 'comments.post_id', 'comments.status', 'posts.title as post_title', 'comments.created_at')
                     ->join('posts','posts.id','=','comments.post_id')
                     ->where('comments.user_id','=', $user_id)
                     ->where('comments.status', '=', Constant::STATUS_ACTIVATED)
                     ->where('posts.status', Constant::STATUS_ACTIVATED)
                     ->orderBy('comments.created_at','desc')
                     ->paginate($paging_info);

            return $query;
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
                $comment->status = Constant::STATUS_ACTIVATED;

                $comment->user()->associate($user);
                $comment->post()->associate($post);

                if($target_comment) $comment->comment_id = $target_comment->id;

                $comment->save();
            }

            return $comment;

        }
    
        //edit a given comment
        public function editComment($request){

            $user = Auth::user();

            $comment = Comment::find($request->get("id"));

            if($user->id != $comment->user_id) return null;

            $update_info = array('title'=>$request->get("title"), 'content'=>$request->get("content"));

            $comment = $comment->update($update_info);

            return $comment;

        }
    
        //delete a comment
        public function deleteComment($request){

        }
    
        //like a comment
        public function likeComment($comment_id){

            $comment = Comment::findOrFail($comment_id);

            return auth()->user()->thumbed()->toggle($comment);
        }

}