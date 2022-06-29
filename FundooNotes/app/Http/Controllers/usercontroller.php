<?php

namespace App\Http\Controllers;

use App\Exceptions\FundoNotesException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Notifications\PasswordResetRequest;
use Symfony\Component\HttpFoundation\Response;
use Mail;

class usercontroller extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/register",
     *   summary="register",
     *   description="register the user for login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"firstname","lastname","email", "password", "password_confirmation"},
     *               @OA\Property(property="firstname", type="string"),
     *               @OA\Property(property="lastname", type="string"),
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password")
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="User successfully registered"),
     * )
     * It takes a POST request and required fields for the user to register
     * and validates them if it validated, creates those field including 
     * values in DataBase and returns success response
     *
     *@return \Illuminate\Http\JsonResponse
     */

    function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string|between:2,100',
                'lastname' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:150',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
            ]);
            if ($validator->fails()) {
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
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   summary="login",
     *   description="login",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string"),
     *               @OA\Property(property="password", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="login Success"),
     *   @OA\Response(response=401, description="we can not find the user with that e-mail address You need to register first"),
     * )
     * login user
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials entered'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::error('Not a Registered Email');
            return response()->json([
                'message' => 'Email is not registered',
            ], 402);
        } elseif (!Hash::check($request->password, $user->password)) {
            Log::error('Wrong Password');
            return response()->json([
                'message' => 'Wrong password'
            ], 403);
        }

        //Request is validated
        //Crean token
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
        Log::info('Login Successful');
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
    }

    /**
     * * @OA\Post(
     *   path="/api/logout",
     *   summary="logout",
     *   description="logout",
     *   @OA\RequestBody(
     *   @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"token"},
     *               @OA\Property(property="token", type="string"),
     *    ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            log::warning('Invalid Authorisation ');
            return response()->json([
                'message' => 'Invalid token'
            ], 400);
        } else {
            JWTAuth::invalidate($request->token);
            log::info('User successfully logged out');
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        }
    }

    /**
     * * @OA\Get(
     *   path="/api/getuser",
     *   summary="getuser",
     *   description="getuser",
     *   @OA\RequestBody(
     *    ),
     *   @OA\Response(response=201, description="Found User successfully"),
     *   @OA\Response(response=401, description="User cannot be found"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * getuser
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUser(Request $request)
    {
        $user = JWTAuth::authenticate($request->token);

        if (!$user) {
            log::error('Invalid authorisation token');
            return response()->json([
                'message' => 'Invalid token'
            ], 400);
        } else {
            return response()->json([
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
            ], 200);
        }
    }


    /**
     *  @OA\Post(
     *   path="/api/forgotPassword",
     *   summary="forgot password",
     *   description="forgot user password",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email"},
     *               @OA\Property(property="email", type="string"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Password Reset link is send to your email"),
     *   @OA\Response(response=400, description="we can not find a user with that email address"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This API Takes the request which is the email id and validates it and check where that email id
     * is present in DataBase or not, if it is not,it returns failure with the appropriate response code and
     * checks for password reset model once the email is valid and calling the function Mail::Send
     * by passing args and successfully sending the password reset link to the specified email id.
     *
     * @return success reponse about reset link.
     */

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'Validation_error' => $validator->errors(),
                ]);
            }
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                log::error('Not a registered email');
                throw new FundoNotesException('Not a Registered Email', 404);
            }

            $token = JWTAuth::fromUser($user);

            if ($user) {
                $data = array('name' => "Rushikesh Patil", "resetlink" => $token);
                Mail::send('mail', $data, function ($message) {
                    $message->to('rushipatil6632@gmail.com', 'abc')->subject('Reset Password');
                    // $message->attach('$token');
                    $message->from('rushipatil6632@gmail.com', 'Rushikesh Patil');
                });

                Log::info('Reset Password Token Sent to your Email');
                return response()->json([
                    'status' => 200,
                    'message' => 'Password Reset link is send to your email'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }


    /**
     *   @OA\Post(
     *   path="/api/resetPassword",
     *   summary="reset password",
     *   description="reset user password",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"new_password","password_confirmation"},
     *               @OA\Property(property="new_password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=200, description="Password reset successfull!"),
     *   @OA\Response(response=400, description="we can't find the user with that e-mail address"),
     *   security={
     *       {"Bearer": {}}
     *     }
     * )
     * This API Takes the request which has new password and confirm password and validates both of them
     * if validation fails returns failure resonse and if it passes it checks with DataBase whether the token
     * is there or not if not returns a failure response and checks the user email also if everything is
     * ok it will reset the password successfully.
     */
    public function resetPassword(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'new_password' => 'min:6|required|',
                'password_confirmation' => 'required|same:new_password',

            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => "Password doesn't match"
                ]);
            }
            $user = JWTAuth::authenticate($request->token);

            if (!$user) {
                Log::error('Invalid Authorization Token');
                throw new FundoNotesException('Invalid Authorization Token', 401);
            } else {
                $user->password = bcrypt($request->new_password);
                $user->save();
                log::info('Password updated successfully');
                return response()->json([
                    'status' => 200,
                    'message' => 'Password Reset Successfull'
                ]);
            }
        } catch (FundoNotesException $exception) {
            return response()->json([
                'message' => $exception->message()
            ], $exception->statusCode());
        }
    }
}