<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;;
use App\Notifications\PasswordResetRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Mail;

class usercontroller extends Controller
{
    function register(Request $request){
        $validator = Validator::make($request->all(),[
            'firstname' => 'required|string|between:2,100',
            'lastname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:150',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors()->tojson(), 400);
        }

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'fails'], 400);
        }
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
    }


    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'logout failed'], 200);
        }
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'token' => $request->token,
                'exception' => $exception,
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|max:100',
        ]);

        if ($validator->fails()){
            return response()->json([
                'Validation_error' => $validator->errors(),
            ]);
        }
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'status' => 400,
                'message' => 'We can not find user with that email id'
            ]);
        }

        else{
            $token = JWTAuth::fromUser($user);
            $data = array('name'=>"Rushikesh Patil", "resetlink"=>$token);
            // $user->notify((new PasswordResetRequest($user->email, $token)));
            Mail::send('mail', $data, function($message) {
                $message->to('rushipatil6632@gmail.com', 'abc')->subject('Reset Password');
                // $message->attach('$token');
                $message->from('rushipatil6632@gmail.com','Rushikesh Patil');
             });
             return response()->json([
                'status' => 200,
                'message' => 'Password Reset link is send to your email'
             ]);
        }
    }

    public function resetPassword(Request $request){
        $validate = Validator::make($request->all(), [
            'new_password' => 'min:6|required|',
            'password_confirmation' => 'required|same:new_password',
            
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 400 ,
                'message' => "Password doesn't match"
            ]);
        }
        $user = JWTAuth::authenticate($request->token);
        
        if(!$user){
            return response()->json([
                'status' => 400,
                'message' => 'we can not find User with such Mail Address',
            ]);
        }
        else{
            $user->password = bcrypt($request->new_password);
            $user->save();
            return response()->json([
                'status' => 200,
                'message' => 'Password Reset Successfull'
            ]);
        }
    }
}