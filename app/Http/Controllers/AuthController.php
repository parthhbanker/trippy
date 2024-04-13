<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request){

        $credentials = $request->only('email','password');

        if(Auth::attempt($credentials)){

            $user = User::find(Auth::id());
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json(['user' => $user , 'access_token' => $token ]);

        }

        return response()->json(['error' => 'invalid credentials'],401);

    }


    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['user' => $user, 'access_token' => $token]);

    }


    public function logout(Request $request){

        $user = Auth::guard('sanctum')->user();
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);

    }

}
