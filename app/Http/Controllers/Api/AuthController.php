<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Controller; 

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Contracts\Auth\AuthService;

class AuthController extends Controller
{

    protected $service;
    public $successStatus = 200;


    /**
     * AuthController constructor.
     * @param $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * call AuthService to register user
     *
     * @param Request $request
     * @return json response
     */
    public function register(Request $request){

        $validator = $this->validateRegister($request);

        $user = $this->service->register($request);

        if($user == null) return response()->json(['message'=>'Failed to register with provided information.']);

        Auth::login($user);
        $success['token'] =  $user->createToken(config('app.name'))->accessToken;
        $cookie = $this->make_token_cookie($success['token']);
        return response()->json(['user'=>Auth::user()], $this->successStatus)->withCookie($cookie); 
    }
    
    /**
     * call AuthService to login user
     *
     * @param Request $request
     * @return json response
     */
    public function login(Request $request){ 

        $validator = $this->validateLogin($request);

        $email = $request->get('email');
        $password = $request->get('password');
        $remember = $request->get('remember');

        $user = $this->service->login($email, $password);

        if($user) {
            Log::debug('User with id: '.$user->id.', name: '.$user->name.' is logged in');

            Auth::login($user);
            $success['token'] =  $user->createToken(config('app.name'))->accessToken;
            $cookie = $this->make_token_cookie($success['token']);
            return response()->json(['user'=>Auth::user()], $this->successStatus)->withCookie($cookie);
        }
        else {
            Log::debug('email: '.$email.' attempts to log in failed');

            return response()->json(['message'=>['Email address is wrong or Password is wrong']], 401);
        }
    }


    public function profile(Request $request){
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    /**
     * call to log out user
     *
     * @param Request $request
     * @return json response
     */
    public function logout(Request $request){
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * validate user information before register user
     *
     * @param Request $request
     * @return null
     */
    protected function validateRegister(Request $request){

        $this->validate($request, [
            'name' => "required|string|max:255|unique:users",
            'email' => "required|string|email|max:255|unique:users",
            'password' => "required|string|min:8",
            'comfirmed_password' => "required|same:password",
        ]);
    }

    /**
     * validate user information before login user
     *
     * @param Request $request
     * @return null
     */
    protected function validateLogin(Request $request){

        $this->validate($request, [
            "email" => "required|email",
            "password" => "required|string",
        ]);
    }

    private function make_token_cookie($token){

        return cookie(env('AUTH_TOKEN'), $token, null, null, null, null, true); //'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
    }


}
