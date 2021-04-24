<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class APILoginController extends Controller {

    /**
     * Get a token via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $credentials = request(['email', 'password']);

        $user = User::where('email',$credentials['email'])->first();

        if(!$user){
            abort(401,__('Invalid username or password.'));
        }


        if (!Hash::check($credentials['password'], $user->password)) {
            abort(401,__('Invalid username or password.'));
        }
        
        if (auth()->attempt($credentials)) {
   
            $token = auth()->user()->createToken('user')->accessToken;
            return response()->json(['message'=>__('Logged in successfully'),'token' => $token,'user' => auth()->user()], 200);
   
        } else {
            abort(401,__('Invalid username or password.'));
        }
    }

    public function register(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => bcrypt($input['password'])
        ]);

        $token = $user->createToken('user')->accessToken;
        return response()->json(['token' => $token,'user' => $user], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => __('Successfully logged out')], 200);; // modify as per your need
    }
}
