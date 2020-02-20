<?php

namespace App\Services\Business;

use App\Contracts\Business\MessageService;

use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

use App\Contracts\Constant;
use App\User; 
use App\UserContact; 

class MessageServiceImp implements MessageService
{
    public function fetchUserContact(){

        $user = Auth::guard('api')->user();

        $contacts = DB::table("user_contacts")
                    ->select('user_contacts.user_id', 'user_contacts.contact_id', 'users.name as contact_name', 'profiles.portrait')
                    ->join('users','users.id','=','user_contacts.contact_id')
                    ->join('profiles','profiles.user_id','=','user_contacts.contact_id')
                    ->where('user_contacts.user_id', $user->id)
                    ->orderBy('user_contacts.updated_at','desc')
                    ->get();

        return $contacts;
    }

    public function addUserContact($contact_name){

        $user = Auth::guard('api')->user();

        $targetUser = User::where('name', '=', $contact_name)->first();

        $contact = UserContact::where('user_id', $user->id)->where('contact_id', $targetUser->id)->first();
        
        if(empty($targetUser) || !empty($contact)) return null;
        else {

            $userContact = new UserContact();

            $userContact->user_id = $user->id;
            $userContact->contact_id = $targetUser->id;
            
            $userContact->save();
        }

        return $this->fetchUserContact();
    }

    public function removeUserContact($contact_id){

        $user = Auth::guard('api')->user();
        
        $result = UserContact::where('user_id', $user->id)
                             ->where('contact_id', $contact_id)
                             ->delete();

        return $this->fetchUserContact();
    }

    public function fetchUserContactMessage($contact_id){

        $user = Auth::guard('api')->user();

        $messages = DB::table('messages')
                        ->select('messages.sender_id', 'messages.receiver_id', 'messages.content', 
                                'messages.type', 'messages.status', 'messages.updated_at')
                        // ->where('messages.sender_id', $user->id)
                        // ->orwhere('messages.receiver_id', $user->id)
                        ->where(
                            function ($messages) use ($user, $contact_id){
                                $messages->where('messages.sender_id', $user->id)
                                         ->where('messages.receiver_id', $contact_id);
                            }
                        )
                        ->orwhere(
                            function ($messages) use ($user, $contact_id){
                                $messages->where('messages.sender_id', $contact_id)
                                         ->where('messages.receiver_id', $user->id);
                            }
                        )
                        ->orderBy('updated_at', 'asc')
                        ->get();
        
        return $messages;
    }

    public function addUserContactMessage($contact_id, $message, $status){

        if(empty($status)) $status=Constant::MESG_STATUS_UNREAD;

        $user = Auth::guard('api')->user();

        $result = DB::table('messages')
                        ->insert(['sender_id'=>$user->id,
                                  'receiver_id'=>$contact_id,
                                  'content'=>$message,
                                  'status'=>$status,
                                  "created_at" => Carbon::now(),
                                  "updated_at" => Carbon::now(),
                                  ]);

        return $result;
    }

    public function removeUserContactMessage($contact_id){

    }
}