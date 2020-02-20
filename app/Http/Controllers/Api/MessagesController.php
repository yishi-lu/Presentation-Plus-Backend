<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 

use App\Contracts\Business\MessageService;
use App\Contracts\Constant;

class MessagesController extends Controller
{
    
    protected $service;
    public $successStatus = 200;

    /**
     * MessagesController constructor.
     * @param $service
     */
    public function __construct(MessageService $service)
    {
        $this->service = $service;
    }

    public function fetchUserContact(Request $request){
        
        $result = $this->service->fetchUserContact();

        if($result){
            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>""], 401); 
        }

    }

    public function addUserContact(Request $request){

        $contact_name = $request->get('contact_name'); 
        
        $result = $this->service->addUserContact($contact_name);

        if($result){
            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>""], 401); 
        }

    }

    public function removeUserContact(Request $request){

        $contact_id = $request->get('contact_id'); 

        $result = $this->service->removeUserContact($contact_id);

        if($result){
            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>""], 401); 
        }

    }

    public function fetchUserContactMessage(Request $request){

        $contact_id = $request->get('contact_id'); 

        $result = $this->service->fetchUserContactMessage($contact_id);

        if($result){
            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>""], 401); 
        }
    }

    public function addUserContactMessage(Request $request){

        $contact_id = $request->get('contact_id'); 
        $message = $request->get('message'); 
        $status = $request->get('status'); 

        $result = $this->service->addUserContactMessage($contact_id, $message, $status);

        if($result){
            return response()->json(['success'=>$result], $this->successStatus); 
        }
        else {
            return response()->json(['message'=>""], 401); 
        }
    }

}
