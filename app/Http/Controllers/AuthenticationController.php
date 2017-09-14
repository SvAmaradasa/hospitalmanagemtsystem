<?php

namespace App\Http\Controllers;

use App\common;
use App\User;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthenticationController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller except for the authenticate method.
        $this->middleware('jwt.auth', ['except' => ['authenticate', 'refresh', 'validateToken', 'register']]);
    }

    /**
     * POST
     * User Authentication method.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        //Get username and password from request
        $credentials = ['username' => $request->input('username'), 'password' => $request->input('password')];

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => common::$EMP_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
            }

            $user = Auth::user();

            if ($user->disabled) {
                return response()->json(['error' => common::$USER_DISABLED], common::$HTTP_NOT_AUTHORIZED);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => common::$UNABLE_TO_CREATE_TOKEN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * GET
     * Get  Authenticated User
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            $user->load('Doctor')->load('Employee');

            return response()->json(compact('user'));
        } catch (JWTException $e) {
            return response()->json(['error' => common::$UNABLE_TO_GET_USER], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate Register Token.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        $registerToken = $request->input('registerToken');

        if ($registerToken == null || $registerToken == "") {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $user = User::where('remember_token', $registerToken)->firstOrFail();
            return response()->json(compact('user'));

        } catch (ModelNotFoundException $e) {
            return response()->json([common::$REGISTER_TOKEN_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_GET_REGISTER_TOKEN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Register User.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        if ($request->input('user') == null || $request->input('user') == '') {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $validator = Validator::make($request->input('user'), User::$createRules);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            }

            $user = User::where('id', $request->input('user.id'))->firstOrFail();
            $user->fill($request->input('user'));
            $user->password = common::encryptPassword($user->password);
            $user->remember_token = null;
            $user->save();

            return response()->json([common::$SUCCESS]);

        } catch (ModelNotFoundException $e) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_GET_REGISTER_TOKEN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
