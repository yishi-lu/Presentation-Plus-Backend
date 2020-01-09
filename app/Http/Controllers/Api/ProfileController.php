<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller; 

use App\User; 
use App\Contracts\Business\ProfileService;
use App\Contracts\Business\BusinessService;
use App\Contracts\Constant;

class ProfileController extends Controller
{
    protected $profile_service;
    protected $business_service;
    protected $successStatus = 200;

    /**
     * AuthController constructor.
     * @param $service
     */
    public function __construct(ProfileService $pservice, BusinessService $bservice)
    {
        $this->profile_service = $pservice;
        $this->business_service = $bservice;
    }

    public function show(Request $request){

        $profile_id = $request->get('profile_id');

        $user_info = $this->profile_service->show_user_profile($profile_id);

        if($request->get('need_post') == 0) {
            $auth_user = Auth::guard('api')->user();
            if(!$auth_user || $auth_user->id != $user_info->id) return response()->json(['message'=>['Unauthorized user']], 401);
        }

        if($user_info && $request->get('need_post') == 1) $user_post = $this->business_service->fetchUserPosts($user_info);
        else $user_post = [];

        if($user_info) {

            return response()->json(['user'=>$user_info, 'posts'=>$user_post], $this->successStatus); 
        }
        else {

            return response()->json(['message'=>['Unable to get user information']], 401);
        }

    }

    public function edit(Request $request){

        $validator = $this->profile_validation($request);
        
        $imagePath = null;
        
        if(request('portrait')){
            //first parameter: the place you want to store (our case: dir with name profile)
            //second parameter: the dreiver you want to use (our case: local storage)
            $imagePath = request('portrait')->store('profile_portrait', 'public');
        }


        $profile_info = array('signature'=>$request->get('signature'),
                              'visibility'=>$request->get('visibility'));
        
        if($imagePath) $profile_info['portrait'] = $imagePath;

        $result = $this->profile_service->edit_user_profile($profile_info);

        if($result) {

            return response()->json(['message'=>$result], $this->successStatus); 
        }
        else {

            return response()->json(['message'=>['Unable to edit user information']], 401);
        }
    }

    public function follow_unfollow(Request $request){

        $profile_id = $request->get('profile_id'); 
        
        $result = $this->profile_service->follow_unfollow($profile_id);

        if($result){
            return response()->json(['message'=>true], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>false], 401); 
        }

    }

    protected function profile_validation(Request $request){
        $this->validate($request, [
            "signature" => "required|max:100",
        ]);
    }



}
