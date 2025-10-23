<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    public function register(Request $request){

        $fields=$request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'role' => ['required', Rule::in([0, 1, 2])],
            'password'=>'required|confirmed'
        ]);

        $user=User::create($fields);

        $token = $user->createToken($request->name);

        return [
            'user'=>$user,
            'token'=>$token->plainTextToken
        ];

    }

     public function login(Request $request){

        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user=User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return [
                'errors'=>[
                    'email'=>['the provided credentials are incorrect.']
                ]
            ];
        }

        $token = $user->createToken($user->name);

        return [
            'user'=>$user,
            'token'=>$token->plainTextToken
        ];

    }

    public function logout(Request $request){

        $request->user()->tokens()->delete();

        return[
            'message'=>'you are logged out'
        ];
    }
}