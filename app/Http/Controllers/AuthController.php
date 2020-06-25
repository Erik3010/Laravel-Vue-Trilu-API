<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Token;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {

        $validate = Validator::make($request->all(), [
            'first_name' => 'required|alpha|between:2,20',
            'last_name' => 'required|alpha|between:2,20',
            'username' => 'unique:App\User|required|regex:/^[A-Za-z._0-9]/|between:5,12',
            'password' => 'between:5,12'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => bcrypt($request->password)
        ]);

        $userId = User::where('username', $request->username)->pluck('id')[0];
        $token = bcrypt($userId);

        Token::create([
            'user_id' => $userId,
            'token' => $token
        ]);

        return response()->json([
            'message' => 'success',
            'token' => $token
        ], 200);
    }

    public function login(Request $request) {

        $validate = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json(['message' => 'invalid field'], 422);
        }

        if(Auth::attempt($request->all())) {
            $userId = Auth::user()->id;
            $token = bcrypt($userId);

            $checkToken = Token::where('user_id', $userId)->first();
            if($checkToken) {
                $checkToken->update([
                    'token' => $token
                ]);
            }else{
                Token::create([
                    'user_id' => $userId,
                    'token' => $token
                ]);
            }

            return response()->json([
                'message' => 'success',
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'invalid login'], 401);
    }

    public function logout(Request $request) {
        $isToken = Token::where('token', $request->token)->first();

        if(!$isToken) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        $isToken->delete();
        return response()->json(['message' => 'logout success'], 200);

    }

}
