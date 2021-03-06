<?php

namespace App\Services\Business;

use App\Contracts\Business\BusinessService;
use App\User; 
use App\Post; 
use App\Post_image; 
use App\Profile; 

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use App\Contracts\Constant;

class BusinessServiceImp implements BusinessService
{

    public $successStatus = 200;
    private $cacheExpirationTime = 10*60;

    /**
     * fetch all posts in database; paging_info, filter and order can be applied
     *
     * @param $filter, $order, $paging_info
     * @return Post::array | null
     */
    public function fetchAllPosts($current_page=1, $paging_info=20, $filter=null, $order=null){

        // dd($current_page);

        // $posts = Redis::get('post:all');

        if(empty($posts)){
            $auth_user = Auth::guard('api')->user();

            $query = null;
            // if(!$auth_user || $auth_user->profile->followedBy()->count() == 0){

            $query = DB::table("posts")
                    ->select('posts.id', 'posts.title', 'posts.image_url', 'posts.type', 'posts.liked', 'posts.viewed', 'posts.user_id', 'users.name', 'posts.created_at')
                    ->join('users','users.id','=','posts.user_id')
                    ->where('posts.visibility', Constant::STATUS_PUBLIC)
                    ->where('users.status', Constant::STATUS_ACTIVATED)
                    ->where('posts.status', Constant::STATUS_ACTIVATED)
                    ->orderBy('posts.created_at','desc')
                    ->get();

            // }
            // else {
                
            //     $profile = $auth_user->profile;

            //     $query = DB::table("posts")
            //             ->select('posts.id', 'posts.title', 'posts.image_url', 'posts.type', 'posts.liked', 'posts.viewed', 'posts.user_id', 'users.name', 'posts.created_at')
            //             ->join('users','users.id','=','posts.user_id')
            //             ->join('profile_user', 'profile_user.user_id', '=', 'posts.user_id')
            //             ->where(
            //                 function ($query) use ($auth_user, $profile){
            //                     $query->where('posts.visibility', Constant::STATUS_FOLLOWER)
            //                           ->where(function ($query) use ($auth_user, $profile){
            //                               $query->where('profile_user.profile_id', '=', $profile->id)
            //                                     ->orwhere('posts.user_id', '=', $auth_user->id);
            //                           });
                                    
            //                 }
            //             )
            //             ->orwhere(
            //                 function ($query) use ($auth_user, $profile) {
            //                     $query->where('posts.visibility', Constant::STATUS_PUBLIC);
                                    
            //                 }
            //             )
            //             ->where('users.status', Constant::STATUS_ACTIVATED)
            //             ->where('posts.status', Constant::STATUS_ACTIVATED)
            //             ->orderBy('posts.id','desc')
            //             ->groupBy('posts.id')
            //             ->distinct()
            //             ->get();

            // }   

            foreach($query as $item){

                $count_thumbed = DB::table("post_thumb_user")
                            ->select("id")
                            ->where("post_id", "=", $item->id)
                            ->count();


                $item->count_thumbed = $count_thumbed;
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
                    'pageName' => "page",
                ]
            );

            // Redis::set('post:all', json_encode($pagination));
            // Redis::expire('post:all:', $this->cacheExpirationTime);

            return $pagination;

            // if($auth_user){
            //     $profile = $auth_user->profile;
            //     $query->join('profile_user', 'profile_user.user_id', '=', 'posts.user_id');

            //     // $query->join('profile_user', function($join) use($profile) {
            //     //     $join->on('profile_user.user_id', '=', 'posts.user_id')
            //     //          ->where('profile_user.profile_id', '=', $profile->id);
            //     // });

            //     $query->where(
            //                 function ($query) use ($auth_user, $profile) {
            //                     $query->where('posts.visibility', Constant::STATUS_FOLLOWER)
            //                           ->where('profile_user.profile_id', '=', $profile->id);
                                    
            //                 }
            //             )
            //             ->orwhere(
            //                 function ($query) use ($auth_user, $profile) {
            //                     $query->where('posts.visibility', Constant::STATUS_PUBLIC)
            //                           ->where('profile_user.profile_id', '=', $profile->id);
                                    
            //                 }
            //             );
            // }
            // else {
            //     $query->where('posts.visibility', Constant::STATUS_PUBLIC);
            // } 

            // $query->where('users.status', Constant::STATUS_ACTIVATED)
            //       ->where('posts.status', Constant::STATUS_ACTIVATED)
            //       ->orderBy('posts.created_at','desc');
                    


            // $posts = $query->paginate($paging_info, 'posts.id');
        }
        
       
        return json_decode($posts);
    }

    /**
     * fetch all posts of a user; paging_info, filter and order can be applied
     *
     * @param $user $filter, $order, $paging_info
     * @return Post::array | null
     */
    public function fetchUserPosts($user, $current_page=1, $paging_info=20, $filter=null, $order=null){

        // $user_posts = Redis::get('post:user:'.$user->id);

        if(empty($user_posts)){

            $auth_user = Auth::guard('api')->user();

            if(!$auth_user || ($auth_user->id != $user->id && ($auth_user->role != CONSTANT::ROLE_SUPER_ADMIN || $auth_user->role != CONSTANT::ROLE_ADMIN))){
                $posts = Post::select('id', 'title', 'image_url', 'type', 'liked', 'viewed')
                        ->where("user_id", $user->id)
                        ->where('visibility', Constant::STATUS_PUBLIC)
                        ->where('status', Constant::STATUS_ACTIVATED)
                        ->orderBy('created_at','desc')
                        ->get();
            }
            else{
                $posts = Post::select('id', 'title', 'image_url', 'type', 'liked', 'viewed')
                ->where("user_id", $user->id)
                ->where('status', Constant::STATUS_ACTIVATED)
                ->orderBy('created_at','desc')
                ->get();
            }

            foreach($posts as $item){

                $count_thumbed = DB::table("post_thumb_user")
                            ->select("id")
                            ->where("post_id", "=", $item->id)
                            ->count();


                $item->count_thumbed = $count_thumbed;
            }

            $page = $current_page;
            $perPage = $paging_info;
            $pagination = new LengthAwarePaginator(
                $posts->forPage($page, $perPage), 
                $posts->count(), 
                $perPage, 
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => "page",
                ]
            );

            // Redis::set('post:user'.$user->id, json_encode($pagination));
            // Redis::expire('post:user:'.$user->id, $this->cacheExpirationTime);

            return $pagination;

        }
        

        return json_decode($user_posts);
    }

    /**
     * fecth detail information of a post by given post id
     *
     * @param $id
     * @return Post | null
     */
    public function fetchOnePost($post_id){

        // $post = Redis::get('post:detail:'.$post_id);

        if(empty($post)){
            $post = Post::find($post_id);

            $post_content_image = Post_image::where('post_id', '=', $post->id)->get();

            $post_author = User::find($post->user_id);

            $post->author_name = $post_author->name; 
            $post->post_content_image = $post_content_image;

            $auth_user = Auth::guard('api')->user();

            if($auth_user){
                $is_follow = DB::table('profile_user')
                            ->select('user_id', 'prifle_id')
                            ->where('user_id', '=', $post->user_id)
                            ->where('profile_id', '=', $auth_user->profile->id)
                            ->count();
                
                $is_collected  = DB::table('post_user')
                                ->select('user_id', 'post_id')
                                ->where('user_id', '=', $auth_user->id)
                                ->where('post_id', '=', $post->id)
                                ->count();

                $is_thumbed = DB::table('post_thumb_user')
                                ->select('user_id', 'post_id')
                                ->where('user_id', '=', $auth_user->id)
                                ->where('post_id', '=', $post->id)
                                ->count(); 
            }
            else {
                $is_follow = 0;
                $is_collected = 0;
                $is_thumbed = 0;
            }

            $post->is_collected = $is_collected;
            $post->is_thumbed = $is_thumbed;

            if($auth_user && ($is_follow > 0 || $auth_user->id == $post->user_id || ($auth_user->role == CONSTANT::ROLE_SUPER_ADMIN || $auth_user->role == CONSTANT::ROLE_ADMIN))){
                // Redis::set('post:detail:'.$post_id, json_encode($post));
                // Redis::expire('post:detail:'.$post_id, $this->cacheExpirationTime);
                return $post;
            }
            else{
                if($post->status == CONSTANT::STATUS_ACTIVATED && $post->visibility == CONSTANT::STATUS_PUBLIC){
                    // Redis::set('post:detail:'.$post_id, json_encode($post));
                    // Redis::expire('post:detail:'.$post_id, $this->cacheExpirationTime);
                    return $post;
                }
                else return null;
            }
        }

        return json_decode($post);

    }

    /**
     * create a post
     *
     * @param $request
     * @return Post|null
     */
    public function createOnePost($request){

        $imagePath = null;
        
        if(request('image_url')){
            //first parameter: the place you want to store (our case: dir with name profile)
            //second parameter: the dreiver you want to use (our case: local storage)
            $imagePath = request('image_url')->store('post_cover', 'public');
        }

        // $files = $request->get('contentImage');
        // dd($files);

        // $im = imagecreatefromstring($files);

        // $files[0]->store('post_content_image', 'public');

        $content_image_path = array();
        if(request('contentImage')){
            $files = request('contentImage');
            foreach($files as $image){
                $path = $image->store('post_content_image', 'public');
                array_push($content_image_path, $path);
            }
        }
        
        $post_info = array('title' => $request->get('title'), 
                           'description' => $request->get('description'),
                           'image_url' => $imagePath == null ? "https://i.picsum.photos/id/366/1000/300.jpg" : $imagePath,
                           'content' => $request->get('content'),
                           'type' => $request->get('type'),
                           'status' => Constant::STATUS_ACTIVATED,
                           'visibility' => $request->get('visibility'),
                           'viewed' => 0,
                           'liked' => 0);

        $post = auth()->user()->post()->create($post_info);

        $count = 1;
        foreach($content_image_path as $path){

            $post->post_image()->create([
                'content_image'=>$path, 
                'order'=>$count
            ]);

            $count++;
        }

        return $post;
    }

    /**
     * edit a by given request information
     *
     * @param $request
     * @return bool|null
     */
    public function editOnePost($request){

        $post_id = $request->get('post_id');

        $post = POST::find($post_id);

        if($post == null) return false;

        $user = Auth::guard('api')->user();

        if($user->id == $post->user_id || $user->role == CONSTANT::ROLE_SUPER_ADMIN){

            $imagePath = null;

        
            if(request('image_url')){
                //first parameter: the place you want to store (our case: dir with name profile)
                //second parameter: the dreiver you want to use (our case: local storage)
                $imagePath = request('image_url')->store('post_cover', 'public');
            }

            //get newly added image
            $content_image_path = array();
            if(request('contentImage')){
                $files = request('contentImage');
                foreach($files as $image){
                        $index = explode('.', explode("###", $image->getClientOriginalName())[1]);
                        $path = $image->store('post_content_image', 'public');
                        $content_image_path[$index[0]] = $path;
                }
            }

            //get previous uploaded image
            if(request('originalImage')){
                $files = request('originalImage');
                foreach($files as $image){
                        $index = explode('###', $image);
                        $content_image_path[$index[1]] = $index[0];
                }
            }


            $post_info = array('title' => request('title'), 
                            'description' => request('description'),
                            'content' => request('content'),
                            'type' => request('type'),
                            'status' => Constant::STATUS_ACTIVATED,
                            'visibility' => request('visibility'));
            if($imagePath) $post_info['image_url'] = $imagePath;

            $result = $post->update($post_info);

            $res=Post_image::where('post_id',$post_id)->delete();

            for($i=0; $i<sizeof($content_image_path); $i++){
                $post->post_image()->create([
                    'content_image'=>$content_image_path[$i], 
                    'order'=>$i
                ]);
            }

            return $result;
        }
        else return false;
    }

    /**
     * delete a post by given request
     *
     * @param $request
     * @return bool|null
     */
    public function deletePost($request){

        $post = POST::find($request->get('post_id'));

        if($post == null) return false;

        $user = Auth::guard('api')->user();

        if($user->id == $post->user_id || $user->role == CONSTANT::ROLE_SUPER_ADMIN){
            
            $result = $post->delete();

            return $result;
        }
        else return false;
    }

    /**
     * collect a post by auth user
     *
     * @param $post_id
     * @return bool|null
     */
    public function collectPost($post_id){

        $post = Post::findOrFail($post_id);

        return auth()->user()->collected()->toggle($post);
    }

    /**
     * fetch all auth user collected posts
     *
     * @param null
     * @return posts|null
     */
    public function fetchCollectedPosts(){

        $auth_user = Auth::guard('api')->user();

        $posts = DB::table('posts')
                ->select('posts.id as post_id', 'posts.title', 'posts.description', 'posts.image_url', 'posts.status', 'posts.visibility', 'users.id as user_id', 'users.name'
                        , 'profiles.id as profile_id', 'profiles.portrait')
                ->join('post_user', 'post_user.post_id', '=', 'posts.id')
                ->join('users', 'users.id', '=', 'posts.user_id')
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->where('post_user.user_id', '=', $auth_user->id)
                ->where('posts.status', '=', Constant::STATUS_ACTIVATED)
                ->where('posts.visibility', '!=', Constant::STATUS_PRIVATE)
                ->orderBy('posts.created_at','desc')
                ->get();

        $followed = DB::table('profile_user')
                    ->select('user_id')
                    ->where('profile_id', '=', $auth_user->profile->id)
                    ->get();

        $user_id_set = collect([]);

        //find user who follow auth user
        foreach($followed as $item){
            $user_id_set->push($item->user_id);
        }

        //remove STATUS_FOLLOWER posts if the user is not following auth user
        foreach($posts as $post){

            if(!$user_id_set->contains($post->user_id) && $post->visibility == Constant::STATUS_FOLLOWER){

                $posts->forget($post);

            }
        }

        return $posts;

    }

    /**
     * thumb/unthumb a post by auth user
     *
     * @param null
     * @return boolean|null
     */
    public function thumbPost($post_id){

        $auth_user = Auth::guard('api')->user();

        $post = Post::findOrFail($post_id);

        $query = DB::table('post_thumb_user')
                ->select('post_id', 'user_id')
                ->where('user_id', $auth_user->id)
                ->where('post_id', $post->id)
                ->count();

        if($query <= 0){
            return DB::table('post_thumb_user')->insert(
                ['user_id' => $auth_user->id, 'post_id' => $post->id]
            );
        }
        else{
            return DB::table('post_thumb_user')
                    ->where('user_id', $auth_user->id)
                    ->where('post_id', $post->id)
                    ->delete();
        }

    }
}