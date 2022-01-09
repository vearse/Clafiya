<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,55',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|numeric|digits:11|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'=> $validator->messages()
            ], 422);
        }
        $request['password']= Hash::make($request->password);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->all());

        $token = $user->createToken('User Access Token')->accessToken;
        $data = compact('user','token'); 
        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'data' => $data
        ], 200);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
       
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'=> $validator->messages()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('User Access Token')->accessToken;
                
                return response()->json([
                    'success' => true,
                    'data' => compact('token', 'user')
                ]);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => "Incorrect Password"
                ], 422);
                
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "'User does not exist'"
            ], 422);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
    
        return response()->json([
            'success' => true,
            'message' => 'You have been successfully logged out!'
        ], 200);
    }

    public function profile(Request $request){
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

}
